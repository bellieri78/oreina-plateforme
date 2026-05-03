<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// Garde-fou : refuse les commandes destructives sur les bases protégées,
// AVANT le boot de Laravel pour garantir l'interception.
// Override : OREINA_ALLOW_DB_WIPE=1 php artisan ...
if (PHP_SAPI === 'cli' && isset($_SERVER['argv'])) {
    $oreinaCmd = $_SERVER['argv'][1] ?? null;
    $oreinaDestructive = ['migrate:fresh', 'migrate:refresh', 'migrate:reset', 'db:wipe'];
    if (in_array($oreinaCmd, $oreinaDestructive, true) && getenv('OREINA_ALLOW_DB_WIPE') !== '1') {
        $oreinaEnvPath = dirname(__DIR__) . '/.env';
        $oreinaDb = null;
        if (is_readable($oreinaEnvPath)) {
            foreach (file($oreinaEnvPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $oreinaLine) {
                if (preg_match('/^\s*DB_DATABASE\s*=\s*"?([^"\s]+)"?\s*$/', $oreinaLine, $oreinaMatches)) {
                    $oreinaDb = $oreinaMatches[1];
                    break;
                }
            }
        }
        $oreinaProtected = ['oreina_local', 'oreina_prod', 'oreina_plateforme'];
        if (in_array($oreinaDb, $oreinaProtected, true)) {
            fwrite(STDERR, "\n");
            fwrite(STDERR, "  REFUS : '{$oreinaCmd}' interdit sur la base protégée '{$oreinaDb}'.\n");
            fwrite(STDERR, "  Cette commande effacerait toutes les données.\n");
            fwrite(STDERR, "  - Pour les tests : la suite phpunit utilise oreina_test (forcé via phpunit.xml).\n");
            fwrite(STDERR, "  - Pour wiper volontairement : OREINA_ALLOW_DB_WIPE=1 php artisan {$oreinaCmd}\n");
            fwrite(STDERR, "\n");
            exit(1);
        }
    }
}

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectGuestsTo('/connexion');

        // Alias pour le middleware de vérification des permissions
        $middleware->alias([
            'permission' => \App\Http\Middleware\CheckPermission::class,
            'admin' => \App\Http\Middleware\CheckAdmin::class,
            'current_member' => \App\Http\Middleware\EnsureCurrentMember::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
