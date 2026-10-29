<?php

namespace App\Console\Commands;

use App\Models\Part;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

/**
 * One-off: adds GST to part prices that were entered without it.
 *
 * G&M loaded their first parts at GST-exclusive prices, but everything online —
 * the shop, the receipt, Google Shopping — treats a price as what the customer
 * pays, GST included. From here on prices are entered GST inclusive; this fixes
 * the parts loaded before that.
 *
 * Only parts created before --before are touched, so anything already loaded
 * at a GST-inclusive price is left alone. It records every change in
 * storage/app and refuses to run again once that record exists, because
 * running it twice would add GST twice.
 */
class AddGstToPrices extends Command
{
    protected const LOG = 'parts-gst-added.json';

    protected $signature = 'parts:add-gst
                            {--before= : Only parts created before this date (YYYY-MM-DD) — when G&M started entering GST-inclusive prices}
                            {--dry-run : Show what would change without saving}';

    protected $description = 'Add GST to part prices entered without it, rounded up to the dollar.';

    public function handle(): int
    {
        if (! $this->option('before')) {
            $this->error('Give --before=YYYY-MM-DD: the day G&M started entering prices with GST included.');

            return self::FAILURE;
        }

        $before = Carbon::parse($this->option('before'))->startOfDay();
        $dryRun = (bool) $this->option('dry-run');

        if (Storage::disk('local')->exists(self::LOG)) {
            $this->error('GST has already been added — see storage/app/' . self::LOG . '. Running it again would add it twice.');

            return self::FAILURE;
        }

        $rate = 1 + (float) config('shipping.gst', 15) / 100;
        $changes = [];

        Part::query()
            ->whereNotNull('price')
            ->where('price', '>', 0)
            ->where('created_at', '<', $before)
            ->chunkById(200, function ($parts) use ($rate, $dryRun, &$changes) {
                foreach ($parts as $part) {
                    $old = (float) $part->price;
                    // Round to cents first so float noise cannot add a dollar.
                    $new = ceil(round($old * $rate, 2));

                    $changes[] = ['id' => $part->id, 'title' => $part->title, 'old' => $old, 'new' => $new];

                    if (! $dryRun) {
                        $part->update(['price' => $new]);
                    }
                }
            });

        if (empty($changes)) {
            $this->info("No priced parts were created before {$before->toDateString()}.");

            return self::SUCCESS;
        }

        $this->table(
            ['Part', 'Was', 'Now'],
            array_map(fn (array $c) => [$c['title'], '$' . number_format($c['old'], 2), '$' . number_format($c['new'], 2)], $changes),
        );

        if ($dryRun) {
            $this->warn(count($changes) . ' prices would change. Dry run — nothing was saved.');

            return self::SUCCESS;
        }

        Storage::disk('local')->put(self::LOG, json_encode([
            'ran_at' => now()->toIso8601String(),
            'before' => $before->toDateString(),
            'changes' => $changes,
        ], JSON_PRETTY_PRINT));

        $this->info(count($changes) . ' prices now include GST. Old prices are in storage/app/' . self::LOG . '.');

        return self::SUCCESS;
    }
}
