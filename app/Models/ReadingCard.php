<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Carta concreta repartida en una posición concreta de una lectura.
 *
 * Es la tabla puente del historial: une `reading`, `spread_position` y `card`
 * y añade la orientación (`invertida`). Los datos de la carta se leen siempre
 * desde `Card`, así el significado puede corregirse sin duplicarlo.
 */
class ReadingCard extends Model
{
    /**
     * Campos asignables en masa.
     *
     * @var list<string>
     */
    protected $fillable = [
        'reading_id',
        'spread_position_id',
        'card_id',
        'invertida',
    ];

    /**
     * Conversiones de tipo.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'invertida' => 'boolean',
    ];

    /**
     * Lectura a la que pertenece la carta.
     */
    public function reading(): BelongsTo
    {
        return $this->belongsTo(Reading::class);
    }

    /**
     * Carta del catálogo.
     */
    public function card(): BelongsTo
    {
        return $this->belongsTo(Card::class);
    }

    /**
     * Posición del tablero que ocupa.
     */
    public function position(): BelongsTo
    {
        return $this->belongsTo(SpreadPosition::class, 'spread_position_id');
    }
}
