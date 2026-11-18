<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Solicitud del enlace de restablecimiento.
 *
 * Por seguridad, el mensaje es el mismo exista o no la cuenta: así no se
 * puede usar este formulario para averiguar correos registrados.
 */
class PasswordResetLinkController extends Controller
{
    /**
     * Muestra el formulario para pedir el enlace.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/ForgotPassword', [
            'status' => session('status'),
        ]);
    }

    /**
     * Envía el enlace de restablecimiento si el correo existe.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        // `sendResetLink` devuelve el estado del envío; el mensaje se muestra
        // igual esté o no registrado el correo.
        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status == Password::RESET_LINK_SENT) {
            return back()->with('status', __($status));
        }

        throw ValidationException::withMessages([
            'email' => [trans($status)],
        ]);
    }
}
