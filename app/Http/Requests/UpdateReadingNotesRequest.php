<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Valida el guardado de las notas personales de una lectura.
 *
 * Las notas son privadas y opcionales. El límite de 2000 caracteres evita
 * abusos sin molestar a quien escribe un diario breve.
 */
class UpdateReadingNotesRequest extends FormRequest
{
    /**
     * La autorización real (dueño de la lectura) se comprueba en el
     * controlador mediante `Reading::isOwnedBy()`.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'notas_personales' => ['nullable', 'string', 'max:2000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'notas_personales' => 'las notas',
        ];
    }
}
