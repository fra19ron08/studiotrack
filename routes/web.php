<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudioController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ClientStudioController;
use App\Http\Controllers\ClientBookingController;

/**
 * HOME: prima pagina = barra ricerca (pubblica)
 */
Route::get('/', [ClientStudioController::class, 'index'])->name('home');

require __DIR__ . '/auth.php';

/**
 * Dashboard + Profile
 */
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        $user = auth()->user();

        if ($user->hasRole('cliente')) {
            return view('dashboard.cliente', compact('user'));
        }

        return view('dashboard.proprietario', compact('user'));
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/**
 * OWNER (proprietario)
 */
Route::middleware(['auth', 'verified', 'role:proprietario'])
    ->prefix('owner')
    ->name('owner.')
    ->group(function () {
        Route::resource('studios', StudioController::class);
    });

/**
 * CLIENT (cliente)
 */
Route::middleware(['auth', 'verified', 'role:cliente'])
    ->prefix('client')
    ->name('client.')
    ->group(function () {

        Route::get('/studios', [ClientStudioController::class, 'index'])->name('studios.index');
        Route::get('/studios/results', [ClientStudioController::class, 'results'])->name('studios.results');

        Route::get('/studios/{studio}', [ClientStudioController::class, 'show'])
            ->whereNumber('studio')
            ->name('studios.show');

        Route::post('/slots/{slotId}/book', [BookingController::class, 'store'])->name('slots.book');

        Route::get('/bookings/{booking}', [ClientBookingController::class, 'show'])->name('bookings.show');
    });
