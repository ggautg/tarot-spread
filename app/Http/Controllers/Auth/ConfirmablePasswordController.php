<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Confirmación de contraseña para acciones sensibles.
 *
 * Laravel pide repetir la contraseña antes de entrar en zonas protegidas
 * (`password.confirm`); el resultado se guarda en sesión durante un tiempo
 * limitado.
 */
class ConfirmablePasswordController extends Controller
{
    /**
     * Muestra la pantalla de confirmación.
     */
    public function show(): Response
    {
        return Inertia::render('Auth/ConfirmPassword');
    }

    /**
     * Valida la contraseña actual y marca la confirmación como vigente.
     */
    public function store(Request $request): RedirectResponse
    {
        if (! Auth::guard('web')->validate([
            'email' => $request->user()->email,
            'password' => $request->password,
        ])) {
            throw ValidationException::withMessages([
                'password' => __('auth.password'),
            ]);
        }

        $request->session()->put('auth.password_confirmed_at', time());

        return redirect()->intended(route('home', absolute: false));
    }
}
