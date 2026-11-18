<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Reading;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Registro de cuentas.
 *
 * La cuenta hereda las lecturas anónimas creadas en esta sesión, para que el
 * visitante no pierda una tirada recién hecha al registrarse.
 */
class RegisteredUserController extends Controller
{
    /**
     * Muestra el formulario de registro.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Crea la cuenta, inicia sesión y adopta las lecturas de la sesión.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // El id se captura antes de crear la cuenta: `Auth::login` no regenera
        // la sesión, pero lo dejamos explícito para no depender de ese detalle.
        $sessionId = $request->session()->getId();

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        Reading::where('session_id', $sessionId)
            ->whereNull('user_id')
            ->update(['user_id' => $user->id]);

        return redirect(route('home', absolute: false));
    }
}
