<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Una posición dentro de una tirada.
 *
 * Guarda dos cosas distintas:
 *  - el significado editorial (`nombre_posicion`, `descripcion`), y
 *  - la geometría del tablero (`coord_x`, `coord_y`, `rotacion`), expresada
 *    en porcentajes para que el tablero sea responsive.
 *
 * `rotacion = 90` permite dibujar la carta cruzada de la Cruz Celta.
 */
class SpreadPosition extends Model
{
    /**
     * Campos asignables en masa.
     *
     * @var list<string>
     */
    protected $fillable = [
        'spread_id',
        'orden',
        'nombre_posicion',
        'descripcion',
        'coord_x',
        'coord_y',
        'rotacion',
    ];

    /**
     * Conversiones de tipo.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'orden' => 'integer',
        'coord_x' => 'float',
        'coord_y' => 'float',
        'rotacion' => 'integer',
    ];

    /**
     * Tirada a la que pertenece la posición.
     */
    public function spread(): BelongsTo
    {
        return $this->belongsTo(Spread::class);
    }

    /**
     * Cartas repartidas en esta posición a lo largo del historial.
     */
    public function readingCards(): HasMany
    {
        return $this->hasMany(ReadingCard::class);
    }
}
