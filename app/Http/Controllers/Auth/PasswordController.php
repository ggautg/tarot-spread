<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

/**
 * Cambio de contraseña desde el perfil.
 *
 * Es distinto del restablecimiento por correo: aquí se exige la contraseña
 * actual y la sesión ya está iniciada.
 */
class PasswordController extends Controller
{
    /**
     * Guarda la contraseña nueva después de validar la actual.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back();
    }
}
