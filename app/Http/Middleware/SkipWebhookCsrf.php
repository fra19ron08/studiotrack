<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SkipWebhookCsrf
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->is('webhook/*')) {
            return $next($request);
        }
        return $next($request);
    }
}
