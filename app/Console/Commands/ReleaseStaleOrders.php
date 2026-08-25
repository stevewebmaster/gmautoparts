<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;

class ReleaseStaleOrders extends Command
{
    protected $signature = 'orders:release-stale';

    protected $description = 'Release parts held by unpaid orders whose checkout window has lapsed.';

    public function handle(): int
    {
        $released = 0;

        Order::query()
            ->stale()
            ->with('items.part')
            ->chunkById(100, function ($orders) use (&$released) {
                foreach ($orders as $order) {
                    $order->release();
                    $released++;

                    $this->line("  released {$order->reference} ({$order->items->count()} parts)");
                }
            });

        $this->info($released === 0 ? 'Nothing to release.' : "Released {$released} stale orders.");

        return self::SUCCESS;
    }
}
