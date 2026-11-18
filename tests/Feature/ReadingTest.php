<?php

namespace Tests\Feature;

use App\Models\Card;
use App\Models\Reading;
use App\Models\Spread;
use App\Models\User;
use App\Services\ReadingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

/**
 * Ciclo completo de una lectura: sellado, revelado, verificación, privacidad
 * de las notas y del secreto, historial y adopción de lecturas al entrar.
 */
class ReadingTest extends TestCase
{
    use RefreshDatabase;

    private Spread $spread;

    protected function setUp(): void
    {
        parent::setUp();

        $this->spread = $this->makeSpread();
    }

    /**
     * Crea una tirada de tres posiciones con tres cartas suficientes.
     */
    private function makeSpread(): Spread
    {
        for ($i = 1; $i <= 3; $i++) {
            Card::create([
                'name' => "Carta $i",
                'name_short' => "test$i",
                'value' => (string) $i,
                'value_int' => $i,
                'type' => 'major',
                'suit' => null,
                'meaning_up' => "Significado al derecho $i.",
                'meaning_rev' => "Significado invertido $i.",
                'desc' => "Descripción $i.",
            ]);
        }

        $spread = Spread::create([
            'nombre' => 'Tres Cartas',
            'slug' => 'tres-cartas',
            'descripcion' => 'Pasado, presente y futuro.',
            'cantidad_cartas' => 3,
        ]);

        foreach ([1, 2, 3] as $orden) {
            $spread->positions()->create([
                'orden' => $orden,
                'nombre_posicion' => "Posición $orden",
                'descripcion' => "Descripción de la posición $orden.",
                'coord_x' => 20 * $orden,
                'coord_y' => 50,
                'rotacion' => 0,
            ]);
        }

        return $spread;
    }

    /**
     * Sella una lectura como invitado y la devuelve.
     */
    private function sealAsGuest(?string $question = null): Reading
    {
        $this->post('/tirada/tres-cartas', ['pregunta' => $question])
            ->assertRedirect();

        return Reading::query()->latest('id')->firstOrFail();
    }

    public function test_guest_can_seal_a_reading_without_drawing_cards(): void
    {
        $reading = $this->sealAsGuest('¿Debo aceptar el trabajo?');

        $this->assertNull($reading->user_id);
        $this->assertNull($reading->revealed_at);
        $this->assertSame('¿Debo aceptar el trabajo?', $reading->pregunta);
        $this->assertNotNull($reading->session_id);
        $this->assertSame(0, $reading->cards()->count());
    }

    public function test_sealed_reading_hides_the_server_secret(): void
    {
        $reading = $this->sealAsGuest();

        // El cliente de pruebas no conserva cookies entre llamadas: se reenvía
        // la cookie de sesión para que el invitado siga siendo el dueño.
        $this->withCookie(config('session.cookie'), $reading->session_id)
            ->get("/lectura/{$reading->uuid}")
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Readings/Show')
                ->where('reading.server_seed_hash', $reading->server_seed_hash)
                ->where('canEdit', true)
                ->where('verified', null)
                ->missing('reading.server_seed')
                ->missing('reading.session_id'));
    }

    public function test_revealing_draws_cards_and_passes_verification(): void
    {
        $reading = $this->sealAsGuest();

        $this->post("/lectura/{$reading->uuid}/revelar")
            ->assertRedirect("/lectura/{$reading->uuid}");

        $reading->refresh();

        $this->assertTrue($reading->isRevealed());
        $this->assertSame(3, $reading->cards()->count());

        $this->get("/lectura/{$reading->uuid}")
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('verified', true)
                ->has('reading.server_seed')
                ->has('reading.cards', 3));
    }

    public function test_revealing_twice_does_not_redraw(): void
    {
        $reading = $this->sealAsGuest();

        $this->post("/lectura/{$reading->uuid}/revelar");
        $first = $reading->cards()->pluck('card_id')->all();

        $this->post("/lectura/{$reading->uuid}/revelar");
        $second = $reading->cards()->pluck('card_id')->all();

        $this->assertCount(3, $first);
        $this->assertSame($first, $second);
    }

    public function test_a_tampered_reading_fails_verification(): void
    {
        $reading = $this->sealAsGuest();
        $this->post("/lectura/{$reading->uuid}/revelar");

        // Se altera una carta guardada: el hash del secreto sigue siendo válido,
        // pero el reparto ya no coincide con la semilla.
        $card = $reading->cards()->firstOrFail();
        $card->update(['invertida' => ! $card->invertida]);

        $this->get("/lectura/{$reading->uuid}")
            ->assertInertia(fn (Assert $page) => $page->where('verified', false));
    }

    public function test_other_users_cannot_read_notes_or_modify_a_reading(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();

        $reading = $this->sealAsGuest();
        $reading->update([
            'user_id' => $owner->id,
            'notas_personales' => 'Nota privada de la dueña.',
        ]);

        $this->actingAs($intruder)
            ->get("/lectura/{$reading->uuid}")
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('canEdit', false)
                ->missing('reading.notas_personales')
                ->missing('reading.server_seed'));

        $this->actingAs($intruder)
            ->patch("/lectura/{$reading->uuid}/notas", ['notas_personales' => 'Intento'])
            ->assertForbidden();

        $this->actingAs($intruder)
            ->delete("/lectura/{$reading->uuid}")
            ->assertForbidden();

        $this->assertSame('Nota privada de la dueña.', $reading->refresh()->notas_personales);
    }

    public function test_owner_can_save_private_notes(): void
    {
        $owner = User::factory()->create();
        $reading = $this->sealAsGuest();
        $reading->update(['user_id' => $owner->id]);

        $this->actingAs($owner)
            ->from("/lectura/{$reading->uuid}")
            ->patch("/lectura/{$reading->uuid}/notas", [
                'notas_personales' => 'Mi interpretación personal.',
            ])
            ->assertRedirect("/lectura/{$reading->uuid}");

        $this->assertSame('Mi interpretación personal.', $reading->refresh()->notas_personales);
    }

    public function test_history_only_lists_readings_owned_by_the_visitor(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();

        $mine = $this->sealAsGuest('Mía');
        $mine->update(['user_id' => $user->id]);

        $theirs = $this->sealAsGuest('Ajena');
        $theirs->update(['user_id' => $other->id]);

        $this->actingAs($user)
            ->get('/mis-lecturas')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Readings/History')
                ->has('readings', 1)
                ->where('readings.0.uuid', $mine->uuid));
    }

    public function test_anonymous_history_is_scoped_to_the_current_session(): void
    {
        // Lectura sellada por otra sesión anónima.
        app(ReadingService::class)
            ->commit($this->spread, null, 'otra-sesion', 'Ajena');

        $this->get('/mis-lecturas')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->has('readings', 0));
    }

    public function test_logging_in_adopts_the_anonymous_readings_of_the_session(): void
    {
        $user = User::factory()->create();

        $reading = $this->sealAsGuest('Antes de entrar');
        $this->assertNull($reading->user_id);

        // Se envía la misma cookie de sesión con la que se selló la lectura.
        $this->withCookie(config('session.cookie'), $reading->session_id)
            ->post('/login', [
                'email' => $user->email,
                'password' => 'password',
            ])->assertRedirect('/');

        $this->assertAuthenticatedAs($user);
        $this->assertSame($user->id, $reading->refresh()->user_id);
    }
}
