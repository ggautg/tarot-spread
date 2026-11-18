<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Tipo de tirada (por ejemplo "Tres Cartas" o "Cruz Celta").
 *
 * Un spread agrupa sus posiciones y define cuántas cartas se reparten. El
 * front usa `cantidad_cartas` para mostrar el resumen y `positions` para
 * dibujar el tablero.
 */
class Spread extends Model
{
    /**
     * Campos asignables en masa.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nombre',
        'slug',
        'descripcion',
        'cantidad_cartas',
    ];

    /**
     * Conversiones de tipo.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'cantidad_cartas' => 'integer',
    ];

    /**
     * Las rutas usan el slug legible ("/tirada/cruz-celta") en lugar del id.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Posiciones de la tirada, siempre en el orden visual correcto.
     */
    public function positions(): HasMany
    {
        return $this->hasMany(SpreadPosition::class)->orderBy('orden');
    }

    /**
     * Lecturas realizadas con esta tirada.
     */
    public function readings(): HasMany
    {
        return $this->hasMany(Reading::class);
    }
}
