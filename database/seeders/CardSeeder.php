<?php

namespace Database\Seeders;

use App\Models\Card;
use Illuminate\Database\Seeder;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

/**
 * Carga el mazo completo (78 cartas) de forma reproducible.
 *
 * Estrategia:
 *  1. `database/data/cards.json` es el catálogo fuente versionado. Evita
 *     depender de la red y de los certificados SSL de cada máquina.
 *  2. `database/data/cards.es.json` aporta nombre y significados en español.
 *  3. Si el archivo local no existe, se intenta la API pública como respaldo.
 *
 * Volver a ejecutar el seeder es seguro: `updateOrCreate` usa `name_short`
 * como clave natural y no duplica cartas.
 */
class CardSeeder extends Seeder
{
    private const API_URL = 'https://tarotapi.dev/api/v1/cards';

    private const LOCAL_FILE = 'data/cards.json';

    private const TRANSLATIONS_FILE = 'data/cards.es.json';

    public function run(): void
    {
        $cards = $this->loadSourceCards();

        if ($cards === []) {
            $this->command?->error('No hay cartas para sembrar: falta el archivo local y la API no respondió.');

            return;
        }

        $translations = $this->loadTranslations();

        foreach ($cards as $card) {
            $short = $card['name_short'] ?? null;

            if ($short === null) {
                continue;
            }

            $es = $translations[$short] ?? [];

            Card::updateOrCreate(
                ['name_short' => $short],
                [
                    'name' => $es['name'] ?? $card['name'],
                    'value' => (string) ($card['value'] ?? ''),
                    'value_int' => (int) ($card['value_int'] ?? 0),
                    'type' => $card['type'] ?? 'minor',
                    'suit' => $card['suit'] ?? null,
                    'meaning_up' => $es['meaning_up'] ?? ($card['meaning_up'] ?? ''),
                    'meaning_rev' => $es['meaning_rev'] ?? ($card['meaning_rev'] ?? ''),
                    // Texto de referencia del mazo original; no se muestra en la UI.
                    'desc' => $card['desc'] ?? '',
                ],
            );
        }

        $this->command?->info(count($cards).' cartas sembradas correctamente.');
    }

    /**
     * Lee el catálogo local o, en su defecto, descarga la API una sola vez.
     *
     * @return array<int, array<string, mixed>>
     */
    private function loadSourceCards(): array
    {
        $localPath = database_path(self::LOCAL_FILE);

        if (File::exists($localPath)) {
            return json_decode(File::get($localPath), true)['cards'] ?? [];
        }

        try {
            $response = Http::timeout(30)->retry(2, 500)->get(self::API_URL);
        } catch (ConnectionException $exception) {
            $this->command?->error('No se pudo descargar el mazo: '.$exception->getMessage());

            return [];
        }

        if ($response->failed()) {
            $this->command?->error('La API del tarot respondió con error '.$response->status().'.');

            return [];
        }

        // Guardamos la descarga para que la próxima ejecución sea offline.
        File::ensureDirectoryExists(dirname($localPath));
        File::put($localPath, json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        return $response->json('cards') ?? [];
    }

    /**
     * Carga la capa de traducción opcional.
     *
     * @return array<string, array{name: string, meaning_up: string, meaning_rev: string}>
     */
    private function loadTranslations(): array
    {
        $path = database_path(self::TRANSLATIONS_FILE);

        if (! File::exists($path)) {
            return [];
        }

        return json_decode(File::get($path), true)['cards'] ?? [];
    }
}
