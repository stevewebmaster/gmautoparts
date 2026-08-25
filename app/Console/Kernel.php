<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        // Sweep newly-uploaded photos down to web size, out of the request.
        $schedule->command('images:optimize')
            ->everyFiveMinutes()
            ->withoutOverlapping(10)
            ->runInBackground();

        // Drain queued jobs. Scheduled rather than run as a supervised daemon so
        // the single schedule:run cron entry is all the server needs, and there
        // is no process to keep alive or restart after a deploy. --max-time keeps
        // each run inside its minute; retry_after in config/queue.php sits above
        // it so a job still running is never released back onto the queue.
        $schedule->command('queue:work --stop-when-empty --tries=3 --max-time=55')
            ->everyMinute()
            ->withoutOverlapping(5)
            ->runInBackground();

        // Put uncollected reservations back on the market. Runs in the morning
        // so released stock is live for the day rather than overnight.
        $schedule->command('reservations:release-expired')
            ->dailyAt('06:00')
            ->withoutOverlapping(10)
            ->runInBackground();
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
    }
}
