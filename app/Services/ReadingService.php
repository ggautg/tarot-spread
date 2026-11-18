<?php

namespace App\Services;

use App\Models\Reading;
use App\Models\Spread;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Orquesta el ciclo de vida de una lectura: compromiso y revelación.
 *
 * Mantener esta lógica fuera del controlador permite probarla, reutilizarla
 * desde la API o desde un comando, y deja el controlador dedicado a HTTP.
 */
class ReadingService
{
    public function __construct(
        private readonly TarotDrawService $drawService,
    ) {}

    /**
     * Crea la lectura y publica el compromiso, sin repartir cartas todavía.
     *
     * @param  string  $sessionId  Sesión actual; permite recuperar la lectura
     *                             aunque el visitante no tenga cuenta.
     */
    public function commit(
        Spread $spread,
        ?User $user,
        string $sessionId,
        ?string $question = null,
    ): Reading {
        $serverSeed = Str::random(40);

        return Reading::create([
            'spread_id' => $spread->id,
            'user_id' => $user?->id,
            'session_id' => $sessionId,
            'server_seed' => $serverSeed,
            'server_seed_hash' => hash('sha256', $serverSeed),
            // Aporte público de aleatoriedad: se muestra desde el inicio y se
            // combina con el secreto al revelar.
            'client_seed' => Str::random(16),
            'pregunta' => $question,
        ]);
    }

    /**
     * Abre el sobre: reparte y persiste las cartas una única vez.
     *
     * Usa una transacción con bloqueo para que dos peticiones simultáneas no
     * repartan cartas dos veces. Si ya estaba revelada, no hace nada.
     */
    public function reveal(Reading $reading): Reading
    {
        return DB::transaction(function () use ($reading): Reading {
            $locked = Reading::query()
                ->whereKey($reading->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            if ($locked->isRevealed()) {
                return $locked;
            }

            $locked->loadMissing('spread.positions');

            $finalSeed = hash('sha256', $locked->server_seed.$locked->client_seed);
            $draw = $this->drawService->draw($finalSeed, $locked->spread->positions->count());

            foreach ($locked->spread->positions as $index => $position) {
                if (! isset($draw[$index])) {
                    break;
                }

                $locked->cards()->create([
                    'spread_position_id' => $position->id,
                    'card_id' => $draw[$index]['card_id'],
                    'invertida' => $draw[$index]['invertida'],
                ]);
            }

            $locked->update(['revealed_at' => now()]);

            return $locked;
        });
    }

    /**
     * Comprueba la integridad de una lectura revelada.
     */
    public function verify(Reading $reading): bool
    {
        return $this->drawService->verify($reading);
    }
}
