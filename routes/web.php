<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudioController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ClientStudioController;
use App\Http\Controllers\ClientBookingController;

/**
 * 🔧 WEBHOOK GIT DEPLOY - SEMPRE IN CIMA (no auth!)
 */
Route::post('/webhook/github', function () {
    try {
        // Git pull
        exec('cd /home/deploy/current && git pull origin main 2>&1', $output);
        
        // Build assets
        exec('cd /home/deploy/current && npm ci 2>&1', $npm1);
        exec('cd /home/deploy/current && npm run build 2>&1', $npm2);
        
        // Laravel
        exec('cd /home/deploy/current && php artisan config:cache 2>&1', $config);
        exec('cd /home/deploy/current && php artisan view:cache 2>&1', $view);
        exec('cd /home/deploy/current && php artisan migrate --force 2>&1', $migrate);
        
        // Restart services
        exec('sudo systemctl restart php8.3-fpm nginx 2>&1', $restart);
        
        return response('Deploy OK! ' . implode("\n", array_merge($output, $npm2, [$migrate[0] ?? ''])));
    } catch (Exception $e) {
        return response('Deploy FAILED: ' . $e->getMessage(), 500);
    }
});

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
