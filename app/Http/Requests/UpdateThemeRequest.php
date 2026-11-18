<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Valida la corriente visual elegida por el usuario.
 *
 * El catálogo completo vive en el front (`resources/js/themes.js`); aquí solo
 * se valida el formato del slug. Si un tema deja de existir, el front cae al
 * tema por defecto sin romper la cuenta.
 */
class UpdateThemeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'theme' => ['required', 'string', 'max:32', 'regex:/^[a-z0-9-]+$/'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'theme' => 'el tema',
        ];
    }
}
