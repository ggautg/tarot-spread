<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReadingController;
use App\Http\Controllers\ThemeController;
use App\Models\Spread;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas públicas
|--------------------------------------------------------------------------
| La portada lista las tiradas disponibles y el resto del flujo de lectura es
| accesible sin cuenta: el historial de invitado se ata a su sesión.
*/

Route::get('/', function () {
    return inertia('Home', [
        'spreads' => Spread::withCount('positions')->get(),
    ]);
})->name('home');

Route::get('/tirada/{spread:slug}', [ReadingController::class, 'create'])->name('readings.create');
Route::post('/tirada/{spread:slug}', [ReadingController::class, 'store'])->name('readings.store');
Route::get('/lectura/{reading:uuid}', [ReadingController::class, 'show'])->name('readings.show');
Route::post('/lectura/{reading:uuid}/revelar', [ReadingController::class, 'reveal'])->name('readings.reveal');
Route::get('/mis-lecturas', [ReadingController::class, 'history'])->name('readings.history');
Route::delete('/lectura/{reading:uuid}', [ReadingController::class, 'destroy'])->name('readings.destroy');
Route::patch('/lectura/{reading:uuid}/notas', [ReadingController::class, 'updateNotes'])->name('readings.notes');

/*
|--------------------------------------------------------------------------
| Rutas autenticadas
|--------------------------------------------------------------------------
| El tema viaja con la cuenta. El perfil conserva las URLs en español para
| mantener la misma voz que el resto de la aplicación.
*/

Route::middleware('auth')->group(function () {
    Route::patch('/tema', [ThemeController::class, 'update'])->name('theme.update');

    Route::get('/perfil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/perfil', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/perfil', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
