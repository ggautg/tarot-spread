<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Una carta del tarot (arcano mayor o menor).
 *
 * El catálogo se carga desde la API pública de tarotapi.dev mediante
 * `CardSeeder`. `name_short` (por ejemplo "ar00") es la clave natural que
 * permite re-sembrar sin duplicar cartas.
 *
 * Las cartas son inmutables para el usuario: una lectura guarda una copia
 * referenciada por `ReadingCard`, de modo que el significado puede
 * actualizarse sin romper el historial.
 */
class Card extends Model
{
    /**
     * Campos asignables en masa.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'name_short',
        'value',
        'value_int',
        'type',
        'suit',
        'meaning_up',
        'meaning_rev',
        'desc',
    ];

    /**
     * Conversiones de tipo.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'value_int' => 'integer',
    ];

    /**
     * Apariciones de esta carta en lecturas.
     */
    public function readingCards(): HasMany
    {
        return $this->hasMany(ReadingCard::class);
    }
}
