<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Reading;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Perfil de la cuenta.
 *
 * Centraliza los datos personales, el borrado de la cuenta y el estado de
 * verificación del correo. El cambio de contraseña vive en
 * `Auth\PasswordController` para mantener este controlador pequeño.
 */
class ProfileController extends Controller
{
    /**
     * Muestra el formulario de perfil.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
        ]);
    }

    /**
     * Guarda los datos personales.
     *
     * Si cambia el correo se invalida la verificación: la dirección nueva debe
     * confirmarse otra vez.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit');
    }

    /**
     * Borra la cuenta y todo lo asociado (lecturas incluidas).
     *
     * Exige la contraseña actual: es una acción irreversible y no debe poder
     * ejecutarse desde una sesión olvidada abierta.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        // La clave foránea pondría `user_id` a nulo y dejaría las lecturas
        // huérfanas; la interfaz promete borrarlas, así que se eliminan antes.
        // `reading_cards` cae por la cascada de la propia lectura.
        Reading::where('user_id', $user->id)->delete();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
