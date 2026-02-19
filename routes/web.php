<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudioController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ClientStudioController;
use App\Http\Controllers\ClientBookingController;

/**
 * 🔧 WEBHOOK GIT DEPLOY - SEMPRE IN CIMA (no auth/CSRF!)
 */
Route::post('/webhook/github', function () {
    try {
        $path = base_path();  // /home/deploy
        
        // Git pull
        exec("cd {$path} && git stash && git pull origin main", $output);
        
        // NPM build
        exec("cd {$path} && npm ci", $npm1);
        exec("cd {$path} && npm run build", $npm2);
        
        // Laravel optimize
        exec("cd {$path} && php artisan config:cache", $config);
        exec("cd {$path} && php artisan view:cache", $view);
        exec("cd {$path} && php artisan route:cache", $route);
        exec("cd {$path} && php artisan migrate --force", $migrate);
        
        // Restart
        exec('sudo systemctl restart php8.4-fpm nginx', $restart);
        
        \Log::info('DEPLOY OK: ' . implode("\n", $output));
        return response('🚀 Deploy OK! ' . now(), 200);
        
    } catch (\Exception $e) {
        \Log::error('DEPLOY FAILED: ' . $e->getMessage());
        return response('❌ Deploy FAILED: ' . $e->getMessage(), 500);
    }
});

/**
 * HOME: barra ricerca pubblica
 */
Route::get('/', [ClientStudioController::class, 'index'])->name('home');

require __DIR__.'/auth.php';

/**
 * DASHBOARD + PROFILE (auth)
 */
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        $user = auth()->user();
        return view($user->hasRole('cliente') ? 'dashboard.cliente' : 'dashboard.proprietario', compact('user'));
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
