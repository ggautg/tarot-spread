<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use App\Models\Card;

class CardSeeder extends Seeder
{
    public function run(): void
    {
        $response = Http::timeout(30)->get('https://tarotapi.dev/api/v1/cards');

        if ($response->failed()) {
            $this->command->error('No se pudo conectar a la Tarot API. Puede ser cold start de Render, reintentá.');
            return;
        }

        $cards = $response->json('cards');

        foreach ($cards as $card) {
            Card::updateOrCreate(
                ['name_short' => $card['name_short']],
                [
                    'name'        => $card['name'],
                    'value'       => $card['value'],
                    'value_int'   => $card['value_int'],
                    'type'        => $card['type'],
                    'suit'        => $card['suit'] ?? null,
                    'meaning_up'  => $card['meaning_up'],
                    'meaning_rev' => $card['meaning_rev'],
                    'desc'        => $card['desc'],
                ]
            );
        }

        $this->command->info(count($cards) . ' cartas cacheadas correctamente.');
    }
}