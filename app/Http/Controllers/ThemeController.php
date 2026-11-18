<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateThemeRequest;
use Illuminate\Http\RedirectResponse;

/**
 * Persiste la corriente visual elegida por el usuario autenticado.
 *
 * El catálogo y los colores viven en el front
 * (`resources/js/themes.js` + `resources/css/app.css`), porque son puramente
 * presentacionales. Aquí solo se guarda el slug validado para que la
 * preferencia viaje con la cuenta.
 */
class ThemeController extends Controller
{
    /**
     * Actualiza el tema del usuario y vuelve a la página anterior.
     */
    public function update(UpdateThemeRequest $request): RedirectResponse
    {
        $request->user()->update([
            'theme' => $request->validated('theme'),
        ]);

        return back();
    }
}
