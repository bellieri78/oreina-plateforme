<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Scheduled Tasks
|--------------------------------------------------------------------------
|
| Taches planifiees pour la gestion automatique des adhesions.
| Pour activer, ajouter cette ligne au crontab du serveur :
| * * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
|
*/

// Mise a jour quotidienne des adhesions expirees (tous les jours a 1h00)
Schedule::command('memberships:update-expired')
    ->dailyAt('01:00')
    ->withoutOverlapping()
    ->onOneServer()
    ->appendOutputTo(storage_path('logs/scheduler.log'));

// Envoi des rappels d'adhesion (tous les jours a 9h00)
Schedule::command('memberships:send-reminders')
    ->dailyAt('09:00')
    ->withoutOverlapping()
    ->onOneServer()
    ->appendOutputTo(storage_path('logs/scheduler.log'));

// Nettoyage des logs anciens (chaque dimanche a 2h00)
Schedule::command('log:clear --keep=30')
    ->weeklyOn(0, '02:00')
    ->withoutOverlapping();

// Purge des tokens expires (chaque jour a 3h00)
Schedule::command('sanctum:prune-expired --hours=24')
    ->dailyAt('03:00')
    ->withoutOverlapping();

// Synchronisation Brevo (chaque jour a 4h00, si configure)
Schedule::command('brevo:sync')
    ->dailyAt('04:00')
    ->withoutOverlapping()
    ->onOneServer()
    ->when(fn () => !empty(config('brevo.api_key')))
    ->appendOutputTo(storage_path('logs/scheduler.log'));
