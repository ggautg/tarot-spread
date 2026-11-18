<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Usuario de la aplicación.
 *
 * Además de la autenticación estándar de Laravel guarda la "corriente visual"
 * (tema) elegida, para que la preferencia viaje con la cuenta entre
 * dispositivos. El catálogo de temas vive en `resources/js/themes.js`.
 */
#[Fillable(['name', 'email', 'password', 'theme'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Atributos que necesitan conversión automática de tipo.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Lecturas creadas por este usuario.
     *
     * Las lecturas anónimas (sin usuario) no aparecen aquí: pertenecen a una
     * sesión, no a una cuenta.
     */
    public function readings(): HasMany
    {
        return $this->hasMany(Reading::class)->latest();
    }
}
