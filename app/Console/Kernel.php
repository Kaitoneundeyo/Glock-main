<?php

namespace App\Console;

use App\Console\Commands\CleanupExpiredReservations;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        CleanupExpiredReservations::class,
    ];
    protected function schedule(Schedule $schedule)
    {
        // Schedule the cleanup command to run every minute
        $schedule->command('reservations:cleanup')->everyMinute();
    }
}
