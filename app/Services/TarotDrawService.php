<?php

namespace App\Services;

use App\Models\Card;
use App\Models\Reading;

/**
 * Motor determinista de reparto de cartas.
 *
 * El resultado depende únicamente de un seed textual, de modo que cualquiera
 * puede recalcularlo y comprobar que la lectura no fue alterada. Se usa el
 * generador Mersenne Twister de PHP (`mt_srand` / `mt_rand`) por dos motivos:
 *
 *  1. Es reproducible entre procesos y versiones de PHP.
 *  2. Las lecturas ya guardadas se calcularon así; cambiarlo invalidaría el
 *     historial existente.
 *
 * La semilla global se restaura en `finally` para no filtrar estado aleatorio
 * al resto de la petición.
 */
class TarotDrawService
{
    /**
     * Reparte `$positionsCount` cartas distintas y decide su orientación.
     *
     * @return array<int, array{card_id: int, invertida: bool}>
     */
    public function draw(string $seed, int $positionsCount): array
    {
        if ($positionsCount <= 0) {
            return [];
        }

        // Solo los primeros 8 bytes hexadecimales: 32 bits de entropía bastan
        // porque el seed completo ya es un SHA-256 del compromiso.
        $numericSeed = hexdec(substr($seed, 0, 8));

        mt_srand((int) $numericSeed);

        try {
            $cardIds = Card::query()->orderBy('id')->pluck('id')->all();

            // Barajado Fisher–Yates: cada carta tiene la misma probabilidad de
            // salir y nunca se repite una misma carta en la tirada.
            for ($i = count($cardIds) - 1; $i > 0; $i--) {
                $j = mt_rand(0, $i);
                [$cardIds[$i], $cardIds[$j]] = [$cardIds[$j], $cardIds[$i]];
            }

            $drawnIds = array_slice($cardIds, 0, min($positionsCount, count($cardIds)));

            return array_map(
                fn (int $cardId): array => [
                    'card_id' => $cardId,
                    'invertida' => (bool) mt_rand(0, 1),
                ],
                $drawnIds,
            );
        } finally {
            // Restaura el generador global a su estado no sembrado.
            mt_srand();
        }
    }

    /**
     * Comprueba que una lectura revelada coincide con su compromiso.
     *
     * Verifica dos cosas:
     *  1. El `server_seed` revelado produce el `server_seed_hash` publicado.
     *  2. Las cartas guardadas son exactamente las que produce el draw.
     */
    public function verify(Reading $reading): bool
    {
        if (! $reading->isRevealed()) {
            return false;
        }

        $hashMatches = hash('sha256', (string) $reading->server_seed) === $reading->server_seed_hash;

        $finalSeed = hash('sha256', $reading->server_seed.$reading->client_seed);
        $expected = $this->draw($finalSeed, $reading->spread->positions->count());

        // `reorder` descarta el `orderBy('id')` del relación `cards()`: con el
        // join, `id` sería ambiguo entre `reading_cards` y `spread_positions`.
        $stored = $reading->cards()
            ->join('spread_positions', 'reading_cards.spread_position_id', '=', 'spread_positions.id')
            ->reorder('spread_positions.orden')
            ->get(['reading_cards.card_id', 'reading_cards.invertida']);

        if ($stored->count() !== count($expected)) {
            return false;
        }

        foreach ($stored as $index => $card) {
            if ((int) $card->card_id !== $expected[$index]['card_id']
                || (bool) $card->invertida !== $expected[$index]['invertida']) {
                return false;
            }
        }

        return $hashMatches;
    }
}
