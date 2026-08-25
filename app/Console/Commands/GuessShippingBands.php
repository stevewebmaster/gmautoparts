<?php

namespace App\Console\Commands;

use App\Enums\ShippingBand;
use App\Models\Part;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

/**
 * One-off helper to get existing stock banded so it can be sold online.
 *
 * This is a STARTING POINT, not an authority. Categories are too coarse on
 * their own — an ECU and a gearbox both live under GEARBOX — so keyword rules
 * matched against the subcategory and title take precedence, and the category
 * only supplies a fallback.
 *
 * By default it only touches parts with no band at all, so it can be re-run
 * safely and will never overwrite a correction made in the admin.
 */
class GuessShippingBands extends Command
{
    protected $signature = 'parts:guess-shipping-bands
                            {--dry-run : Show what would change without saving}
                            {--force : Also re-band parts that already have a band (overwrites corrections)}';

    protected $description = 'Assign a starting shipping band to parts based on category and title keywords.';

    /**
     * Checked against "{subcategory} {title}", first match wins, so order
     * matters: more specific phrases must come before looser ones.
     */
    /**
     * Checked against "{subcategory} {title}", first match wins.
     *
     * Ordered smallest to largest deliberately: a specific component name must
     * beat a general assembly word, or "Gearbox ECU" bands as a whole gearbox
     * because the title happens to mention one.
     */
    protected array $keywordBands = [
        // Fits a courier bag.
        'ecu' => ShippingBand::Small,
        'control module' => ShippingBand::Small,
        'control unit' => ShippingBand::Small,
        'sensor' => ShippingBand::Small,
        'solenoid' => ShippingBand::Small,
        'check strap' => ShippingBand::Small,
        'cable' => ShippingBand::Small,
        'repeater' => ShippingBand::Small,
        'driving light' => ShippingBand::Small,
        'clock spring' => ShippingBand::Small,
        'resistor' => ShippingBand::Small,
        'heater tap' => ShippingBand::Small,
        'over flow bottle' => ShippingBand::Small,
        'fuel flap' => ShippingBand::Small,
        'speaker cover' => ShippingBand::Small,
        'fuse box' => ShippingBand::Small,
        'coil pack' => ShippingBand::Small,
        'distributor' => ShippingBand::Small,
        'egr valve' => ShippingBand::Small,
        'shifter' => ShippingBand::Small,
        'guages' => ShippingBand::Small,
        'gauges' => ShippingBand::Small,

        // Boxed, one person lifts it.
        'alternator' => ShippingBand::Medium,
        'starter' => ShippingBand::Medium,
        'headlight' => ShippingBand::Medium,
        'tail lamp' => ShippingBand::Medium,
        'instrument cluster' => ShippingBand::Medium,
        'water pump' => ShippingBand::Medium,
        'steering pump' => ShippingBand::Medium,
        'air conditioning pump' => ShippingBand::Medium,
        'heater fan' => ShippingBand::Medium,
        'heater control' => ShippingBand::Medium,
        'throttle body' => ShippingBand::Medium,
        'air flow metre' => ShippingBand::Medium,
        'lower arm' => ShippingBand::Medium,
        'control arm' => ShippingBand::Medium,
        'glovebox' => ShippingBand::Medium,
        'airbag' => ShippingBand::Medium,
        'air bag' => ShippingBand::Medium,
        'intake manifold' => ShippingBand::Medium,
        'exhaust manifold' => ShippingBand::Medium,
        'shroud' => ShippingBand::Medium,
        'front cover' => ShippingBand::Medium,

        // Bulky but still couriable.
        'bonnet' => ShippingBand::Large,
        'bootlid' => ShippingBand::Large,
        'tailgate' => ShippingBand::Large,
        'bumper' => ShippingBand::Large,
        'guard' => ShippingBand::Large,
        'grille' => ShippingBand::Large,
        'door lh' => ShippingBand::Large,
        'door rh' => ShippingBand::Large,
        'radiator' => ShippingBand::Large,
        'exhaust' => ShippingBand::Large,
        'carpet' => ShippingBand::Large,
        'hoodlining' => ShippingBand::Large,
        'driveshaft' => ShippingBand::Large,
        'driveaxle' => ShippingBand::Large,
        'steering rack' => ShippingBand::Large,
        'cradle' => ShippingBand::Large,
        'centre console' => ShippingBand::Large,
        'tank' => ShippingBand::Large,
        'wheel' => ShippingBand::Large,

        // Too big or heavy to courier at a flat rate.
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

    /** Fallback when no keyword matches, by category name. */
    protected array $categoryBands = [
        'NOW DISMANTLING' => ShippingBand::QuoteOnly,
        'ENGINE' => ShippingBand::QuoteOnly,
        'GEARBOX' => ShippingBand::QuoteOnly,
        'DIFFERENTIAL' => ShippingBand::Large,
        'BODYPANELS & FRONT PARTS' => ShippingBand::Large,
        'REAR BODY PARTS & PANELS' => ShippingBand::Large,
        'DOOR' => ShippingBand::Large,
        'INTERIOR' => ShippingBand::Large,
        'EXHAUST SYSTEM' => ShippingBand::Large,
        'WHEELS' => ShippingBand::Large,
        'DASH' => ShippingBand::Medium,
        'STEERING' => ShippingBand::Medium,
        'FRONT SUSPENSION' => ShippingBand::Medium,
        'REAR SUSPENSION' => ShippingBand::Medium,
        'ENGINE COOLING' => ShippingBand::Medium,
        'FUEL DELIVERY' => ShippingBand::Medium,
        'BRAKES' => ShippingBand::Medium,
        'HEAT & A/C' => ShippingBand::Medium,
        'LIGHTS' => ShippingBand::Medium,
        'ELECTRICAL' => ShippingBand::Small,
        // SPECIALS is deliberately absent: too mixed to guess, left unbanded.
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
            $this->info($force ? 'No parts found.' : 'Every part already has a shipping band. Use --force to re-band.');

            return self::SUCCESS;
        }

        $this->newLine();
        $this->table(
            ['Band', 'Parts'],
            collect(ShippingBand::cases())
                ->map(fn (ShippingBand $b) => [$b->shortLabel(), $counts[$b->value] ?? 0])
                ->push(['Left unbanded (no rule matched)', $skipped])
                ->all(),
        );

        $sellable = ($counts[ShippingBand::Small->value] ?? 0)
            + ($counts[ShippingBand::Medium->value] ?? 0)
            + ($counts[ShippingBand::Large->value] ?? 0);

        $this->line("  {$sellable} of {$total} parts would become sellable online.");

        if ($dryRun) {
            $this->newLine();
            $this->warn('Dry run — nothing was saved. Re-run without --dry-run to apply.');
        } else {
            $this->newLine();
            $this->info('Done. These are estimates — review them in the admin, especially anything large or awkward.');
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
