<?php

namespace App\Console\Commands;

use App\Models\Reservation;
use Illuminate\Console\Command;

class ReleaseExpiredReservations extends Command
{
    protected $signature = 'reservations:release-expired';

    protected $description = 'Release uncollected reservations back to available so held stock does not quietly rot.';

    public function handle(): int
    {
        $released = 0;

        Reservation::query()
            ->due()
            ->with('part')
            ->chunkById(100, function ($reservations) use (&$released) {
                foreach ($reservations as $reservation) {
                    $reservation->expire();
                    $released++;

                    $this->line("  released {$reservation->reference} — {$reservation->part_title}");
                }
            });

        $this->info($released === 0
            ? 'Nothing to release.'
            : "Released {$released} expired " . str($released === 1 ? 'reservation' : 'reservations') . '.');

        return self::SUCCESS;
    }
}
