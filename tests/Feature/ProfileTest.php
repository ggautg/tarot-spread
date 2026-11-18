<?php

namespace Tests\Feature;

use App\Models\Reading;
use App\Models\Spread;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/perfil');

        $response->assertOk();
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/perfil', [
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/perfil');

        $user->refresh();

        $this->assertSame('Test User', $user->name);
        $this->assertSame('test@example.com', $user->email);
        $this->assertNull($user->email_verified_at);
    }

    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/perfil', [
                'name' => 'Test User',
                'email' => $user->email,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/perfil');

        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    public function test_user_can_delete_their_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete('/perfil', [
                'password' => 'password',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/');

        $this->assertGuest();
        $this->assertNull($user->fresh());
    }

    public function test_correct_password_must_be_provided_to_delete_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/perfil')
            ->delete('/perfil', [
                'password' => 'wrong-password',
            ]);

        $response
            ->assertSessionHasErrors('password')
            ->assertRedirect('/perfil');

        $this->assertNotNull($user->fresh());
    }

    public function test_deleting_the_account_also_deletes_its_readings(): void
    {
        $user = User::factory()->create();

        $spread = Spread::create([
            'nombre' => 'Tres Cartas',
            'slug' => 'tres-cartas',
            'descripcion' => 'Pasado, presente y futuro.',
            'cantidad_cartas' => 3,
        ]);

        $reading = Reading::create([
            'spread_id' => $spread->id,
            'user_id' => $user->id,
            'session_id' => 'sesion-de-prueba',
            'server_seed' => 'secreto',
            'server_seed_hash' => hash('sha256', 'secreto'),
            'client_seed' => 'publica',
        ]);

        $this
            ->actingAs($user)
            ->delete('/perfil', ['password' => 'password'])
            ->assertRedirect('/');

        $this->assertNull($reading->fresh());
    }
}
