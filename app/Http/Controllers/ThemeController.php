<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ThemeController extends Controller
{
    /**
     * Guarda la corriente visual elegida por el usuario.
     *
     * El catálogo de temas vive en resources/js/themes.js (es puramente
     * presentacional), así que acá solo validamos que sea un slug sano.
     * Si algún día el slug deja de existir, el front cae al tema por defecto.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'theme' => ['required', 'string', 'max:32', 'regex:/^[a-z0-9-]+$/'],
        ]);

        $request->user()->update(['theme' => $validated['theme']]);

        return back();
    }
}
