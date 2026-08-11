<?php

namespace Database\Seeders;

use App\Models\Spread;
use Illuminate\Database\Seeder;

class SpreadSeeder extends Seeder
{
    public function run(): void
    {
        // --- Tirada de 3 Cartas ---
        $tresCartas = Spread::updateOrCreate(
            ['slug' => 'tres-cartas'],
            [
                'nombre' => 'Tres Cartas',
                'descripcion' => 'Tirada simple: pasado, presente y futuro.',
                'cantidad_cartas' => 3,
            ]
        );

        $tresCartas->positions()->delete();
        $tresCartas->positions()->createMany([
            ['orden' => 1, 'nombre_posicion' => 'Pasado', 'descripcion' => 'Lo que influyó en la situación actual.', 'coord_x' => 20, 'coord_y' => 50, 'rotacion' => 0],
            ['orden' => 2, 'nombre_posicion' => 'Presente', 'descripcion' => 'El momento actual, el núcleo de la consulta.', 'coord_x' => 50, 'coord_y' => 50, 'rotacion' => 0],
            ['orden' => 3, 'nombre_posicion' => 'Futuro', 'descripcion' => 'Hacia dónde se dirige la situación.', 'coord_x' => 80, 'coord_y' => 50, 'rotacion' => 0],
        ]);

        // --- Cruz Celta ---
        $cruzCelta = Spread::updateOrCreate(
            ['slug' => 'cruz-celta'],
            [
                'nombre' => 'Cruz Celta',
                'descripcion' => 'La tirada clásica de 10 cartas para lecturas profundas.',
                'cantidad_cartas' => 10,
            ]
        );

        $cruzCelta->positions()->delete();
        $cruzCelta->positions()->createMany([
            ['orden' => 1,  'nombre_posicion' => 'Situación actual',   'descripcion' => 'El corazón del asunto.',                     'coord_x' => 35, 'coord_y' => 50, 'rotacion' => 0],
            ['orden' => 2,  'nombre_posicion' => 'Desafío',            'descripcion' => 'Lo que cruza u obstaculiza.',                 'coord_x' => 35, 'coord_y' => 50, 'rotacion' => 90],
            ['orden' => 3,  'nombre_posicion' => 'Base',               'descripcion' => 'El fundamento, raíz del asunto.',             'coord_x' => 35, 'coord_y' => 80, 'rotacion' => 0],
            ['orden' => 4,  'nombre_posicion' => 'Pasado reciente',    'descripcion' => 'Lo que se está retirando.',                   'coord_x' => 10, 'coord_y' => 50, 'rotacion' => 0],
            ['orden' => 5,  'nombre_posicion' => 'Corona',             'descripcion' => 'Lo que se busca conscientemente.',            'coord_x' => 35, 'coord_y' => 20, 'rotacion' => 0],
            ['orden' => 6,  'nombre_posicion' => 'Futuro cercano',     'descripcion' => 'Lo que se aproxima.',                         'coord_x' => 60, 'coord_y' => 50, 'rotacion' => 0],
            ['orden' => 7,  'nombre_posicion' => 'Tú mismo',           'descripcion' => 'Tu actitud frente a la situación.',           'coord_x' => 85, 'coord_y' => 85, 'rotacion' => 0],
            ['orden' => 8,  'nombre_posicion' => 'Entorno',            'descripcion' => 'Influencias externas, gente alrededor.',      'coord_x' => 85, 'coord_y' => 65, 'rotacion' => 0],
            ['orden' => 9,  'nombre_posicion' => 'Esperanzas y miedos', 'descripcion' => 'Lo que se anhela o se teme.',                 'coord_x' => 85, 'coord_y' => 45, 'rotacion' => 0],
            ['orden' => 10, 'nombre_posicion' => 'Resultado final',    'descripcion' => 'Hacia dónde converge todo.',                  'coord_x' => 85, 'coord_y' => 25, 'rotacion' => 0],
        ]);

        $this->command->info('Spreads sembrados: Tres Cartas (3 posiciones) y Cruz Celta (10 posiciones).');
    }
}
