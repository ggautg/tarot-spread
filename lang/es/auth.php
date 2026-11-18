<?php

/*
|--------------------------------------------------------------------------
| Mensajes de autenticación
|--------------------------------------------------------------------------
| Se usan en LoginRequest, ConfirmablePasswordController y en el middleware
| de autenticación. Mantener estos textos asegura que ningún error del
| framework aparezca en inglés.
*/

return [
    'failed' => 'Las credenciales no coinciden con nuestros registros.',
    'password' => 'La contraseña no es correcta.',
    'throttle' => 'Demasiados intentos de acceso. Vuelve a intentarlo en :seconds segundos.',
];
