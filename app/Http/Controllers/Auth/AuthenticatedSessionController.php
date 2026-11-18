<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Reading;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Inicio y cierre de sesión.
 *
 * Al entrar se "adoptan" las lecturas anónimas de la sesión actual: si el
 * visitante selló una tirada antes de entrar, pasa a pertenecer a su cuenta y
 * deja de depender de la cookie del navegador.
 */
class AuthenticatedSessionController extends Controller
{
    /**
     * Muestra el formulario de acceso.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    /**
     * Valida las credenciales, inicia sesión y adopta las lecturas anónimas.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // Se guarda el id ANTES de regenerar: al iniciar sesión Laravel emite
        // una sesión nueva y perderíamos la referencia a las lecturas previas.
        $sessionId = $request->session()->getId();

        $request->authenticate();

        $request->session()->regenerate();

        Reading::where('session_id', $sessionId)
            ->whereNull('user_id')
            ->update(['user_id' => Auth::id()]);

        return redirect()->intended(route('home', absolute: false));
    }

    /**
     * Cierra la sesión y rota el token CSRF.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
