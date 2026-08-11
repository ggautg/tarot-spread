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

    public function store(Request $request, Spread $spread)
    {
        $request->validate([
            'pregunta' => 'nullable|string|max:255',
        ]);

        // Seed reproducible: combina timestamp + random + spread, igual filosofía
        // que el hash SHA-256 de El Corazón de las Cartas.
        $seed = hash('sha256', Str::uuid() . now()->timestamp . $spread->id);

        $reading = Reading::create([
            'spread_id'  => $spread->id,
            'user_id'    => $request->user()?->id,
            'session_id' => $request->session()->getId(),
            'seed'       => $seed,
            'pregunta'   => $request->pregunta,
        ]);

        $draw = $this->computeDraw($seed, $spread);

        foreach ($spread->positions()->orderBy('orden')->get() as $index => $position) {
            $reading->cards()->create([
                'spread_position_id' => $position->id,
                'card_id'            => $draw[$index]['card_id'],
                'invertida'          => $draw[$index]['invertida'],
            ]);
        }

        return redirect()->route('readings.show', $reading->uuid);
    }

    /**
     * Calcula, de forma puramente determinística a partir del seed, qué cartas
     * salen y en qué orientación. No toca la base — es la misma función que usa
     * store() para persistir y verify() para comprobar que nada fue manipulado.
     *
     * @return array<int, array{card_id: int, invertida: bool}> indexado igual
     *         que las posiciones del spread ordenadas por 'orden'.
     */
    private function computeDraw(string $seed, Spread $spread): array
    {
        $numericSeed = hexdec(substr($seed, 0, 8));
        mt_srand($numericSeed);

        // orderBy('id') explícito: garantiza que el barajado parta siempre
        // de la misma secuencia base, sin depender del orden físico en disco.
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

        mt_srand(); // reseteamos la semilla para no afectar otros random del request

        return $result;
    }

    public function show(Reading $reading)
    {
        $reading->load(['spread.positions', 'cards.card', 'cards.position']);

        return Inertia::render('Readings/Show', [
            'reading'  => $reading,
            'verified' => $this->verify($reading),
        ]);
    }

    /**
     * Recalcula la tirada desde el seed guardado y compara, carta por carta
     * y orientación por orientación, contra lo que quedó persistido.
     * Si algo no coincide, alguien tocó la base a mano después de la tirada.
     */
    private function verify(Reading $reading): bool
    {
        $draw = $this->computeDraw($reading->seed, $reading->spread);

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

        return true;
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