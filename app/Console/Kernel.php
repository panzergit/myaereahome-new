<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // setup in secondary server
        // $schedule->command('aereahome:check_primary_down')->everyMinute();

        $schedule->command('aereahome:check_primary_active')->everyMinute();

        $schedule->command('aereahome:system_state_update')->everyMinute();
        $schedule->command('aereahome:sync_push')->everyMinute()->withoutOverlapping();
        $schedule->command('aereahome:clear_change_logs')->daily()->withoutOverlapping();

        $schedule->command('send_invoice_notification:command')->everyMinute();
        $schedule->command('announcement_user:command')->everyMinute();
        $schedule->command('announcement:command')->everyFiveMinutes();
        $schedule->command('aereahome:delete_inbox_messages')->monthlyOn(1, '02:00')->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
