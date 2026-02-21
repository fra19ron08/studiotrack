<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Process;

class WebhookController extends Controller
{
    public function github(Request $request)
    {
        // Verifica secret (da .env GITHUB_WEBHOOK_SECRET)
        if ($request->header('X-GitHub-Event') !== 'push' || $request->header('X-Hub-Signature-256') !== 'sha256='.hash_hmac('sha256', $request->getContent(), env('GITHUB_WEBHOOK_SECRET'))) {
            abort(401);
        }

        $projectPath = base_path();
        Process::run('cd ' . $projectPath . ' && git pull origin main');
        Process::run('cd ' . $projectPath . ' && composer install --no-dev --optimize-autoloader');
        Process::run('cd ' . $projectPath . ' && php artisan migrate --force');
        Process::run('cd ' . $projectPath . ' && php artisan optimize:clear');
        Process::run('cd ' . $projectPath . ' && npm ci && npm run build');
        
        return 'Deploy OK!';
    }
}
