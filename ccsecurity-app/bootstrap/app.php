<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->withSchedule(function (Schedule $schedule) {
        // Run all cleanup tasks daily at midnight
        $schedule->command('cleanup:run-all')
                 ->dailyAt('00:00');

        // Alternative: Run individual commands with specific schedules
        // Delete old entry logs (notifications for guards) - uses table-specific settings
        $schedule->command('notifications:cleanup-old')
                 ->dailyAt('00:00');

        // Delete old visit requests and notifications - uses table-specific settings
        $schedule->command('visitrequests:cleanup-old')
                 ->dailyAt('00:00');

        // Delete old shift logs - uses table-specific settings
        $schedule->command('shiftlogs:cleanup-old')
                 ->dailyAt('00:00');

        // Delete old shift assignments
        $schedule->command('shifts:cleanup-old')
                 ->dailyAt('00:00');
    })
    ->create();
