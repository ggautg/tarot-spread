<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\Reading;
use App\Models\Spread;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class ReadingController extends Controller
{
    public function create(Spread $spread)
    {
        return Inertia::render('Readings/Create', [
            'spread' => $spread->load('positions'),
        ]);
    }

    /**
     * Paso 1 — COMMIT: el servidor genera su seed secreto y publica solo
     * el hash. A partir de acá, el servidor ya no puede cambiar de resultado.
     */
    public function store(Request $request, Spread $spread)
    {
        $request->validate([
            'pregunta' => 'nullable|string|max:255',
        ]);

        $serverSeed = Str::random(40);

        $reading = Reading::create([
            'spread_id'        => $spread->id,
            'user_id'          => $request->user()?->id,
            'session_id'       => $request->session()->getId(),
            'server_seed'      => $serverSeed, // se guarda ya, pero no se muestra hasta el reveal
            'server_seed_hash' => hash('sha256', $serverSeed),
            'client_seed'      => Str::random(16), // aporte de aleatoriedad público desde el inicio
            'pregunta'         => $request->pregunta,
        ]);

        return redirect()->route('readings.show', $reading->uuid);
    }

    /**
     * Paso 2 — REVEAL: abre el sobre. Combina server_seed + client_seed,
     * calcula la tirada, la persiste, y marca revealed_at para que no se
     * pueda volver a revelar (y así tampoco recalcular con otro resultado).
     */
    public function reveal(Reading $reading)
    {
        if ($reading->revealed_at !== null) {
            return redirect()->route('readings.show', $reading->uuid);
        }

        $finalSeed = hash('sha256', $reading->server_seed . $reading->client_seed);
        $draw = $this->computeDraw($finalSeed, $reading->spread);

        foreach ($reading->spread->positions()->orderBy('orden')->get() as $index => $position) {
            $reading->cards()->create([
                'spread_position_id' => $position->id,
                'card_id'            => $draw[$index]['card_id'],
                'invertida'          => $draw[$index]['invertida'],
            ]);
        }

        $reading->update(['revealed_at' => now()]);

        return redirect()->route('readings.show', $reading->uuid);
    }

    /**
     * Calcula, de forma puramente determinística a partir del seed final,
     * qué cartas salen y en qué orientación. No toca la base.
     *
     * @return array<int, array{card_id: int, invertida: bool}>
     */
    private function computeDraw(string $seed, Spread $spread): array
    {
        $numericSeed = hexdec(substr($seed, 0, 8));
        mt_srand($numericSeed);

        $allCardIds = Card::orderBy('id')->pluck('id')->toArray();
        $positionsCount = $spread->positions()->count();

        for ($i = count($allCardIds) - 1; $i > 0; $i--) {
            $j = mt_rand(0, $i);
            [$allCardIds[$i], $allCardIds[$j]] = [$allCardIds[$j], $allCardIds[$i]];
        }

        $drawnCardIds = array_slice($allCardIds, 0, $positionsCount);

        $result = [];
        foreach ($drawnCardIds as $cardId) {
            $result[] = [
                'card_id'   => $cardId,
                'invertida' => (bool) mt_rand(0, 1),
            ];
        }

        mt_srand();

        return $result;
    }

    public function show(Reading $reading)
    {
        $reading->load(['spread.positions', 'cards.card', 'cards.position']);

        return Inertia::render('Readings/Show', [
            'reading'  => $reading,
            'verified' => $reading->revealed_at ? $this->verify($reading) : null,
        ]);
    }

    /**
     * Verificación en dos partes:
     * 1) el server_seed revelado realmente corresponde al hash publicado al comprometer
     * 2) la tirada persistida realmente sale de combinar ese server_seed + client_seed
     * Si cualquiera de las dos falla, algo fue manipulado después del commit.
     */
    private function verify(Reading $reading): bool
    {
        $hashMatches = hash('sha256', $reading->server_seed) === $reading->server_seed_hash;

        $finalSeed = hash('sha256', $reading->server_seed . $reading->client_seed);
        $draw = $this->computeDraw($finalSeed, $reading->spread);

        $storedCards = $reading->cards()
            ->join('spread_positions', 'reading_cards.spread_position_id', '=', 'spread_positions.id')
            ->orderBy('spread_positions.orden')
            ->select('reading_cards.card_id', 'reading_cards.invertida')
            ->get();

        if ($storedCards->count() !== count($draw)) {
            return false;
        }

        foreach ($storedCards as $index => $stored) {
            if ((int) $stored->card_id !== $draw[$index]['card_id']
                || (bool) $stored->invertida !== $draw[$index]['invertida']) {
                return false;
            }
        }

        return $hashMatches;
    }

    public function history(Request $request)
    {
        $query = Reading::with('spread')->latest();

        if ($request->user()) {
            $query->where('user_id', $request->user()->id);
        } else {
            $query->where('session_id', $request->session()->getId())->whereNull('user_id');
        }

        return Inertia::render('Readings/History', [
            'readings' => $query->get(),
        ]);
    }
}