<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Aviso de correo sin verificar.
 *
 * Si la cuenta ya está verificada, redirige a la portada en lugar de mostrar
 * una pantalla sin contenido útil.
 */
class EmailVerificationPromptController extends Controller
{
    /**
     * Muestra el aviso o redirige si el correo ya está verificado.
     */
    public function __invoke(Request $request): RedirectResponse|Response
    {
        return $request->user()->hasVerifiedEmail()
                    ? redirect()->intended(route('home', absolute: false))
                    : Inertia::render('Auth/VerifyEmail', ['status' => session('status')]);
    }
}
