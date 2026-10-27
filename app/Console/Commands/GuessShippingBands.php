<?php

namespace App\Console\Commands;

use App\Enums\ShippingBand;
use App\Models\Part;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

/**
 * One-off helper to size the parts that can be sized without a tape measure.
 *
 * Only two calls are safe from a part's name: small parts that go in an E20
 * satchel, and truck freight. Anything boxed has to be measured — box sizes
 * span more than $200 of South Island freight, and guessing them is how
 * headlights ended up at $20. Those are left unsized for the yard to measure
 * in the Parts Loader app (Manage Stock → Needs freight size).
 *
 * Categories are too coarse on their own — an ECU and a gearbox both live
 * under GEARBOX — so keyword rules matched against the subcategory and title
 * take precedence, and the category only supplies a fallback.
 *
 * By default it only touches parts with no size at all, so it can be re-run
 * safely and will never overwrite a correction made in the admin.
 */
class GuessShippingBands extends Command
{
    protected $signature = 'parts:guess-shipping-bands
                            {--dry-run : Show what would change without saving}
                            {--force : Also re-size parts that already have a size (overwrites corrections)}';

    protected $description = 'Size parts that are obviously satchel or truck freight, from category and title keywords.';

    /**
     * Checked against "{subcategory} {title}", first match wins.
     *
     * Ordered smallest to largest deliberately: a specific component name must
     * beat a general assembly word, or "Gearbox ECU" sizes as a whole gearbox
     * because the title happens to mention one.
     *
     * A null means "boxed — measure it". It still has to match and stop here,
     * or "Engine Alternator" would fall through to the ENGINE category and be
     * marked truck freight.
     */
    protected array $keywordBands = [
        // Fits an E20 satchel.
        'ecu' => ShippingBand::SatchelE20,
        'control module' => ShippingBand::SatchelE20,
        'control unit' => ShippingBand::SatchelE20,
        'sensor' => ShippingBand::SatchelE20,
        'solenoid' => ShippingBand::SatchelE20,
        'check strap' => ShippingBand::SatchelE20,
        'cable' => ShippingBand::SatchelE20,
        'repeater' => ShippingBand::SatchelE20,
        'driving light' => ShippingBand::SatchelE20,
        'clock spring' => ShippingBand::SatchelE20,
        'resistor' => ShippingBand::SatchelE20,
        'heater tap' => ShippingBand::SatchelE20,
        'fuel flap' => ShippingBand::SatchelE20,
        'speaker cover' => ShippingBand::SatchelE20,
        'coil pack' => ShippingBand::SatchelE20,
        // Before 'door lh', so "Door LH Exterior Handle" is not a whole door.
        'handle' => ShippingBand::SatchelE20,

        // Boxed — measure it.
        'over flow bottle' => null,
        'fuse box' => null,
        'distributor' => null,
        'egr valve' => null,
        'shifter' => null,
        'guages' => null,
        'gauges' => null,
        'alternator' => null,
        'starter' => null,
        'headlight' => null,
        'tail lamp' => null,
        'instrument cluster' => null,
        'water pump' => null,
        'vacuum pump' => null,
        'steering pump' => null,
        'air conditioning pump' => null,
        'heater fan' => null,
        'heater control' => null,
        'throttle body' => null,
        'air flow metre' => null,
        'lower arm' => null,
        'control arm' => null,
        'glovebox' => null,
        'airbag' => null,
        'air bag' => null,
        'intake manifold' => null,
        'exhaust manifold' => null,
        'shroud' => null,
        'front cover' => null,
        'guard' => null,
        'grille' => null,
        'radiator' => null,
        'exhaust' => null,
        'carpet' => null,
        'hoodlining' => null,
        'driveshaft' => null,
        'driveaxle' => null,
        'steering rack' => null,
        'cradle' => null,
        'centre console' => null,
        'tank' => null,
        'wheel' => null,

        // Truck freight. G&M: "bumpers, doors, gearboxes, engines are all
        // truck items". Bonnets, bootlids and tailgates are bigger again.
        'bonnet' => ShippingBand::QuoteOnly,
        'bootlid' => ShippingBand::QuoteOnly,
        'tailgate' => ShippingBand::QuoteOnly,
        'bumper' => ShippingBand::QuoteOnly,
        'door lh' => ShippingBand::QuoteOnly,
        'door rh' => ShippingBand::QuoteOnly,
        'engine assembl' => ShippingBand::QuoteOnly,
        'engine conversion' => ShippingBand::QuoteOnly,
        'cylinder block' => ShippingBand::QuoteOnly,
        'cylinder head' => ShippingBand::QuoteOnly,
        'transmission' => ShippingBand::QuoteOnly,
        'transfer case' => ShippingBand::QuoteOnly,
        'gearbox' => ShippingBand::QuoteOnly,
        'nosecut' => ShippingBand::QuoteOnly,
        'nose cut' => ShippingBand::QuoteOnly,
        'front 1/2' => ShippingBand::QuoteOnly,
        'front half' => ShippingBand::QuoteOnly,
        'rear half' => ShippingBand::QuoteOnly,
        'roof cut' => ShippingBand::QuoteOnly,
        'quater' => ShippingBand::QuoteOnly,
        'quarter' => ShippingBand::QuoteOnly,
        'diff head' => ShippingBand::QuoteOnly,
        'rear assembl' => ShippingBand::QuoteOnly,
        'dash assembly' => ShippingBand::QuoteOnly,
        'seat' => ShippingBand::QuoteOnly,
    ];

    /**
     * Fallback when no keyword matches, by category name. Only categories
     * that are truck freight through and through — every other category
     * holds parts of too many sizes to guess. DOOR is not one: mirrors and
     * regulators live there too, and whole doors are caught by keyword.
     */
    protected array $categoryBands = [
        'NOW DISMANTLING' => ShippingBand::QuoteOnly,
        'ENGINE' => ShippingBand::QuoteOnly,
        'GEARBOX' => ShippingBand::QuoteOnly,
    ];

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $force = (bool) $this->option('force');

        $query = Part::query()->with(['category', 'subcategory']);

        if (! $force) {
            $query->whereNull('shipping_band');
        }

        $counts = [];
        $skipped = 0;
        $total = 0;

        $query->chunkById(200, function ($parts) use (&$counts, &$skipped, &$total, $dryRun) {
            foreach ($parts as $part) {
                $total++;
                $band = $this->bandFor($part);

                if (! $band) {
                    $skipped++;
                    continue;
                }

                $counts[$band->value] = ($counts[$band->value] ?? 0) + 1;

                if (! $dryRun) {
                    $part->update(['shipping_band' => $band]);
                }
            }
        });

        if ($total === 0) {
            $this->info($force ? 'No parts found.' : 'Every part already has a freight size. Use --force to re-size.');

            return self::SUCCESS;
        }

        $this->newLine();
        $this->table(
            ['Freight size', 'Parts'],
            collect(ShippingBand::cases())
                ->filter(fn (ShippingBand $b) => isset($counts[$b->value]))
                ->map(fn (ShippingBand $b) => [$b->shortLabel(), $counts[$b->value]])
                ->push(['Left to measure', $skipped])
                ->all(),
        );

        $sellable = collect(ShippingBand::shippable())->sum(fn (string $value) => $counts[$value] ?? 0);

        $this->line("  {$sellable} of {$total} parts would become sellable online.");

        if ($dryRun) {
            $this->newLine();
            $this->warn('Dry run — nothing was saved. Re-run without --dry-run to apply.');
        } else {
            $this->newLine();
            $this->info('Done. Measure the rest in the Parts Loader app: Manage Stock → Needs freight size.');
        }

        return self::SUCCESS;
    }

    protected function bandFor(Part $part): ?ShippingBand
    {
        $haystack = Str::lower(trim(
            ($part->subcategory?->name ?? '') . ' ' . ($part->title ?? '')
        ));

        if ($haystack === '') {
            return $this->categoryBands[$part->category?->name] ?? null;
        }

        foreach ($this->keywordBands as $keyword => $band) {
            // Anchored at a word start, but deliberately not at the end, so
            // "engine assembl" still matches "engine assemblies" while "ecu"
            // no longer matches the middle of "nosecut".
            if (preg_match('/\\b' . preg_quote($keyword, '/') . '/', $haystack) === 1) {
                return $band;
            }
        }

        return $this->categoryBands[$part->category?->name] ?? null;
    }
}
