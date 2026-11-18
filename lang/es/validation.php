<?php

/*
|--------------------------------------------------------------------------
| Mensajes de validación en español
|--------------------------------------------------------------------------
| Traducción de las reglas usadas por la aplicación. Si se añade una regla
| nueva, conviene agregar aquí su mensaje para no mezclar idiomas.
*/

return [
    'accepted' => 'Debes aceptar :attribute.',
    'active_url' => ':attribute no es una URL válida.',
    'after' => ':attribute debe ser una fecha posterior a :date.',
    'alpha' => ':attribute solo puede contener letras.',
    'alpha_dash' => ':attribute solo puede contener letras, números, guiones y guiones bajos.',
    'alpha_num' => ':attribute solo puede contener letras y números.',
    'array' => ':attribute debe ser una lista.',
    'before' => ':attribute debe ser una fecha anterior a :date.',
    'between' => [
        'array' => ':attribute debe tener entre :min y :max elementos.',
        'file' => ':attribute debe pesar entre :min y :max kilobytes.',
        'numeric' => ':attribute debe estar entre :min y :max.',
        'string' => ':attribute debe tener entre :min y :max caracteres.',
    ],
    'boolean' => ':attribute debe ser verdadero o falso.',
    'confirmed' => 'La confirmación de :attribute no coincide.',
    'current_password' => 'La contraseña actual no es correcta.',
    'date' => ':attribute no es una fecha válida.',
    'date_equals' => ':attribute debe ser una fecha igual a :date.',
    'date_format' => ':attribute no coincide con el formato :format.',
    'different' => ':attribute y :other deben ser diferentes.',
    'digits' => ':attribute debe tener :digits dígitos.',
    'digits_between' => ':attribute debe tener entre :min y :max dígitos.',
    'email' => ':attribute debe ser un correo electrónico válido.',
    'ends_with' => ':attribute debe terminar con uno de los siguientes valores: :values.',
    'exists' => ':attribute seleccionado no es válido.',
    'file' => ':attribute debe ser un archivo.',
    'filled' => ':attribute debe tener un valor.',
    'gt' => [
        'array' => ':attribute debe tener más de :value elementos.',
        'file' => ':attribute debe pesar más de :value kilobytes.',
        'numeric' => ':attribute debe ser mayor que :value.',
        'string' => ':attribute debe tener más de :value caracteres.',
    ],
    'gte' => [
        'array' => ':attribute debe tener :value elementos o más.',
        'file' => ':attribute debe pesar :value kilobytes o más.',
        'numeric' => ':attribute debe ser mayor o igual que :value.',
        'string' => ':attribute debe tener :value caracteres o más.',
    ],
    'image' => ':attribute debe ser una imagen.',
    'in' => ':attribute seleccionado no es válido.',
    'integer' => ':attribute debe ser un número entero.',
    'ip' => ':attribute debe ser una dirección IP válida.',
    'ipv4' => ':attribute debe ser una dirección IPv4 válida.',
    'ipv6' => ':attribute debe ser una dirección IPv6 válida.',
    'json' => ':attribute debe ser un JSON válido.',
    'lowercase' => ':attribute debe estar en minúsculas.',
    'lt' => [
        'array' => ':attribute debe tener menos de :value elementos.',
        'file' => ':attribute debe pesar menos de :value kilobytes.',
        'numeric' => ':attribute debe ser menor que :value.',
        'string' => ':attribute debe tener menos de :value caracteres.',
    ],
    'lte' => [
        'array' => ':attribute no debe tener más de :value elementos.',
        'file' => ':attribute debe pesar :value kilobytes o menos.',
        'numeric' => ':attribute debe ser menor o igual que :value.',
        'string' => ':attribute debe tener :value caracteres o menos.',
    ],
    'max' => [
        'array' => ':attribute no debe tener más de :max elementos.',
        'file' => ':attribute no debe pesar más de :max kilobytes.',
        'numeric' => ':attribute no debe ser mayor que :max.',
        'string' => ':attribute no debe tener más de :max caracteres.',
    ],
    'mimes' => ':attribute debe ser un archivo de tipo: :values.',
    'mimetypes' => ':attribute debe ser un archivo de tipo: :values.',
    'min' => [
        'array' => ':attribute debe tener al menos :min elementos.',
        'file' => ':attribute debe pesar al menos :min kilobytes.',
        'numeric' => ':attribute debe ser al menos :min.',
        'string' => ':attribute debe tener al menos :min caracteres.',
    ],
    'not_in' => ':attribute seleccionado no es válido.',
    'not_regex' => 'El formato de :attribute no es válido.',
    'numeric' => ':attribute debe ser un número.',
    'password' => 'La contraseña no es correcta.',
    'present' => ':attribute debe estar presente.',
    'regex' => 'El formato de :attribute no es válido.',
    'required' => ':attribute es obligatorio.',
    'required_if' => ':attribute es obligatorio cuando :other es :value.',
    'required_unless' => ':attribute es obligatorio a menos que :other esté en :values.',
    'required_with' => ':attribute es obligatorio cuando :values está presente.',
    'required_with_all' => ':attribute es obligatorio cuando :values están presentes.',
    'required_without' => ':attribute es obligatorio cuando :values no está presente.',
    'required_without_all' => ':attribute es obligatorio cuando ninguno de :values está presente.',
    'same' => ':attribute y :other deben coincidir.',
    'size' => [
        'array' => ':attribute debe contener :size elementos.',
        'file' => ':attribute debe pesar :size kilobytes.',
        'numeric' => ':attribute debe ser :size.',
        'string' => ':attribute debe tener :size caracteres.',
    ],
    'starts_with' => ':attribute debe comenzar con uno de los siguientes valores: :values.',
    'string' => ':attribute debe ser texto.',
    'timezone' => ':attribute debe ser una zona horaria válida.',
    'unique' => ':attribute ya está en uso.',
    'uploaded' => 'No se pudo subir :attribute.',
    'url' => ':attribute debe ser una URL válida.',
    'uuid' => ':attribute debe ser un UUID válido.',

    /*
    |--------------------------------------------------------------------------
    | Mensajes personalizados
    |--------------------------------------------------------------------------
    */

    'custom' => [
        'email' => [
            'unique' => 'Ese correo electrónico ya está registrado.',
        ],
        'password' => [
            'confirmed' => 'Las contraseñas no coinciden.',
            'min' => 'La contraseña debe tener al menos :min caracteres.',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Nombres legibles de los campos
    |--------------------------------------------------------------------------
    */

    'attributes' => [
        'name' => 'el nombre',
        'email' => 'el correo electrónico',
        'password' => 'la contraseña',
        'password_confirmation' => 'la confirmación de contraseña',
        'current_password' => 'la contraseña actual',
        'pregunta' => 'la pregunta',
        'notas_personales' => 'las notas',
        'theme' => 'el tema',
    ],
];
