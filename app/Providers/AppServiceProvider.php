<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Redirect dinamico dashboard per ruolo
        Blade::directive('dashboardRoute', function ($expression) {
            return "<?php echo auth()->check() && auth()->user()->hasRole('cliente') ? '/dashboard/cliente' : '/dashboard/proprietario'; ?>";
        });
    }
}

