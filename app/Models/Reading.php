<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * Una lectura (tirada) realizada por un usuario o por una sesión anónima.
 *
 * Esquema de compromiso y revelación (commit–reveal):
 *  1. Al crear la lectura se genera `server_seed` (secreto) y se publica su
 *     hash `server_seed_hash`. Desde ese momento el resultado queda
 *     comprometido: el servidor ya no puede cambiarlo sin delatar el hash.
 *  2. Al revelar se combina `server_seed` con `client_seed`, se reparten las
 *     cartas y se guarda `revealed_at`. Una lectura revelada es inmutable.
 *
 * `uuid` es la identidad pública usada en los enlaces compartibles.
 */
class Reading extends Model
{
    /**
     * Campos asignables en masa.
     *
     * @var list<string>
     */
    protected $fillable = [
        'uuid',
        'spread_id',
        'user_id',
        'session_id',
        'pregunta',
        'notas_personales',
        'server_seed',
        'server_seed_hash',
        'client_seed',
        'revealed_at',
    ];

    /**
     * Conversiones de tipo.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'revealed_at' => 'datetime',
    ];

    /**
     * Genera el UUID público antes de insertar la fila.
     */
    protected static function booted(): void
    {
        static::creating(function (self $reading): void {
            $reading->uuid ??= (string) Str::uuid();
        });
    }

    /**
     * Tirada utilizada.
     */
    public function spread(): BelongsTo
    {
        return $this->belongsTo(Spread::class);
    }

    /**
     * Cuenta propietaria, o null si la lectura es anónima.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Cartas repartidas, en el orden en que fueron asignadas a las posiciones.
     */
    public function cards(): HasMany
    {
        return $this->hasMany(ReadingCard::class)->orderBy('id');
    }

    /**
     * Limita la consulta a las lecturas que puede ver el visitante actual.
     *
     * Un usuario autenticado ve las suyas; un invitado ve únicamente las de su
     * propia sesión, nunca las de otras sesiones anónimas.
     */
    public function scopeVisibleTo(Builder $query, ?User $user, ?string $sessionId): Builder
    {
        if ($user !== null) {
            return $query->where('user_id', $user->id);
        }

        return $query->whereNull('user_id')->where('session_id', $sessionId);
    }

    /**
     * Indica si la lectura pertenece a la cuenta o a la sesión indicadas.
     */
    public function isOwnedBy(?User $user, ?string $sessionId): bool
    {
        if ($user !== null) {
            return $this->user_id === $user->id;
        }

        return $this->user_id === null && $this->session_id === $sessionId;
    }

    /**
     * ¿Ya se abrió el sobre? Una lectura revelada no se vuelve a calcular.
     */
    public function isRevealed(): bool
    {
        return $this->revealed_at !== null;
    }
}
