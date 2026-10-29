<?php

namespace App\Enums;

/**
 * A part's freight size: the smallest NZ Couriers satchel or box it goes in.
 *
 * These are the same options the yard picks in eDespatchIT when it prints a
 * label, so sizing a part at load time is the measuring the yard would have
 * done at the label anyway — just done once, up front.
 *
 * Cases are declared smallest to largest, and that order is relied on: a part
 * can always travel in something bigger that takes its weight, so it is
 * charged whichever of those is cheapest to that island. That is what the yard
 * does by hand — the courier rates are not monotonic, so a part that fits an
 * E60 goes North as a 0.015 m³ box but South in the satchel.
 */
enum ShippingBand: string
{
    case SatchelE20 = 'e20';
    case SatchelE60 = 'e60';
    // The same bag, which takes 15 kg. Sized separately because a part over
    // 10 kg cannot go North as a 0.015 m³ box, which only takes 10 kg.
    case SatchelE60Heavy = 'e60_heavy';
    case Box015 = 'box_015';
    case Box025 = 'box_025';
    case Box030 = 'box_030';
    case Box035 = 'box_035';
    case Box040 = 'box_040';
    case Box050 = 'box_050';
    case Box060 = 'box_060';
    case Box075 = 'box_075';
    case Box085 = 'box_085';
    case Box100 = 'box_100';
    case Box125 = 'box_125';

    /**
     * Too big or heavy for a courier — bumpers, doors, gearboxes, engines go by
     * truck and are priced per destination. Not sold online at all; the part
     * page falls back to reserve/enquire.
     */
    case QuoteOnly = 'quote_only';

    public function label(): string
    {
        return match ($this) {
            self::SatchelE20 => 'E20 satchel (door handles, sensors, small parts)',
            self::SatchelE60 => 'E60 satchel, up to 10 kg',
            self::SatchelE60Heavy => 'E60 satchel, 10–15 kg',
            self::QuoteOnly => 'Truck freight — quote only, not sold online',
            default => $this->shortLabel() . ' box, up to ' . $this->maxKg() . ' kg',
        };
    }

    public function shortLabel(): string
    {
        return match ($this) {
            self::SatchelE20 => 'E20 satchel',
            self::SatchelE60 => 'E60 satchel',
            self::SatchelE60Heavy => 'E60 satchel 10–15 kg',
            self::QuoteOnly => 'Truck / quote',
            default => rtrim(rtrim(number_format($this->cube(), 3), '0'), '.') . ' m³',
        };
    }

    public function color(): string
    {
        return match (true) {
            $this === self::QuoteOnly => 'gray',
            $this->isSatchel() => 'success',
            $this->cube() <= 0.035 => 'info',
            default => 'warning',
        };
    }

    /**
     * What it holds, in cubic metres: a box's volume as eDespatchIT rates it,
     * or roughly what goes in a satchel (G&M tried boxes in an E60). Null for
     * an E20, which nobody has measured.
     */
    public function cube(): ?float
    {
        return match ($this) {
            self::SatchelE60, self::SatchelE60Heavy => 0.009,
            self::Box015 => 0.015,
            self::Box025 => 0.025,
            self::Box030 => 0.03,
            self::Box035 => 0.035,
            self::Box040 => 0.04,
            self::Box050 => 0.05,
            self::Box060 => 0.06,
            self::Box075 => 0.075,
            self::Box085 => 0.085,
            self::Box100 => 0.1,
            self::Box125 => 0.125,
            default => null,
        };
    }

    /** Heaviest part this size covers. Null for an E20. */
    public function maxKg(): ?int
    {
        return match (true) {
            $this === self::SatchelE60 => 10,
            $this === self::SatchelE60Heavy => 15,
            $this === self::Box015 => 10,
            $this === self::Box025 => 15,
            $this === self::Box030 => 20,
            $this->cube() !== null => 25,
            default => null,
        };
    }

    public function isSatchel(): bool
    {
        return in_array($this, [self::SatchelE20, self::SatchelE60, self::SatchelE60Heavy], true);
    }

    /** Sizes that can actually be bought and shipped online. */
    public static function shippable(): array
    {
        return array_values(array_diff(self::values(), [self::QuoteOnly->value]));
    }

    public function isShippable(): bool
    {
        return $this !== self::QuoteOnly;
    }

    /**
     * Freight the customer pays for a part this size to the given region, GST
     * inclusive, in whole dollars.
     */
    public function rate(string $region): float
    {
        return $this->shipAs($region)?->cost($region) ?? 0.0;
    }

    /**
     * What to send a part this size in: the cheapest option from this size
     * upward that takes its weight. Printed on the yard's order email so the
     * label matches what the customer was charged.
     */
    public function shipAs(string $region): ?self
    {
        if (! $this->isShippable()) {
            return null;
        }

        $cases = self::cases();
        $cheapest = null;

        foreach (array_slice($cases, array_search($this, $cases, true)) as $option) {
            // Under-ticketing the weight is penalised, so a 12 kg part that
            // fits an E60 never goes as a 10 kg box.
            if ($this->maxKg() !== null && $option->maxKg() !== null && $option->maxKg() < $this->maxKg()) {
                continue;
            }

            $cost = $option->cost($region);

            if ($cost !== null && ($cheapest === null || $cost < $cheapest->cost($region))) {
                $cheapest = $option;
            }
        }

        return $cheapest;
    }

    /**
     * What sending in exactly this satchel or box costs, as the customer pays
     * it, rounded up to the dollar: a box's courier rate plus the variable
     * adjustment plus GST, or a satchel's price plus GST (its fuel charge is
     * already in it). Null if there is no rate for it.
     */
    protected function cost(string $region): ?float
    {
        if (! $this->isShippable()) {
            return null;
        }

        $gst = 1 + (float) config('shipping.gst', 15) / 100;

        if ($this->isSatchel()) {
            $bag = $this === self::SatchelE20 ? 'e20' : 'e60';
            $price = config("shipping.satchels.{$bag}");

            return $price === null ? null : ceil(round((float) $price * $gst, 2));
        }

        $base = config("shipping.courier_rates.{$this->value}.{$region}");

        if ($base === null) {
            return null;
        }

        $adjustment = 1 + (float) config('shipping.variable_adjustment', 0) / 100;

        // Round to cents first so float noise cannot push a whole-dollar
        // amount up a dollar.
        return ceil(round((float) $base * $adjustment * $gst, 2));
    }

    /** Freight to each region, for showing alongside a part or a size. */
    public function rates(): array
    {
        $rates = [];

        foreach (array_keys(config('shipping.regions')) as $region) {
            $rates[$region] = $this->rate($region);
        }

        return $rates;
    }

    public static function options(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }

        return $options;
    }

    /**
     * Options with each size's freight alongside, for whoever is choosing the
     * size: "0.03 m³ box, up to 20 kg — North $23 · South $68".
     */
    public static function optionsWithRates(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->value] = $case->isShippable()
                ? $case->label() . ' — ' . $case->ratesSummary()
                : $case->label();
        }

        return $options;
    }

    /** "North $23 · South $68", or "$12 anywhere" when both are the same. */
    public function ratesSummary(): string
    {
        $rates = $this->rates();

        if (count(array_unique($rates)) === 1) {
            return '$' . number_format(reset($rates)) . ' anywhere';
        }

        return collect($rates)
            ->map(fn (float $rate, string $region) => strtok(config("shipping.regions.{$region}"), ' ') . ' $' . number_format($rate))
            ->implode(' · ');
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
