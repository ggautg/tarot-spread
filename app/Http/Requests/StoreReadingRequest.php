<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Valida la creación de una lectura.
 *
 * La pregunta es opcional: el oráculo funciona igual sin ella. Se limita a
 * 255 caracteres para que quepa en la columna `readings.pregunta` sin
 * truncamientos silenciosos.
 */
class StoreReadingRequest extends FormRequest
{
    /**
     * Cualquier visitante (con cuenta o anónimo) puede consultar.
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
            'pregunta' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Nombres legibles para los mensajes de error.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'pregunta' => 'la pregunta',
        ];
    }
}
