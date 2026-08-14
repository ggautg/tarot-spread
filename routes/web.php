<?php

use App\Models\Spread;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return inertia('Home', [
        'spreads' => Spread::withCount('positions')->get(),
    ]);
})->name('home');

use App\Http\Controllers\ReadingController;

Route::get('/tirada/{spread:slug}', [ReadingController::class, 'create'])->name('readings.create');
Route::post('/tirada/{spread:slug}', [ReadingController::class, 'store'])->name('readings.store');
Route::get('/lectura/{reading:uuid}', [ReadingController::class, 'show'])->name('readings.show');
Route::post('/lectura/{reading:uuid}/revelar', [ReadingController::class, 'reveal'])->name('readings.reveal');
Route::get('/mis-lecturas', [ReadingController::class, 'history'])->name('readings.history');
Route::delete('/lectura/{reading:uuid}', [ReadingController::class, 'destroy'])->name('readings.destroy');
Route::patch('/lectura/{reading:uuid}/notas', [ReadingController::class, 'updateNotes'])->name('readings.notes');

require __DIR__.'/auth.php';
