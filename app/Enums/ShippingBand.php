<?php

namespace App\Enums;

enum ShippingBand: string
{
    case Small = 'small';
    case Medium = 'medium';
    case Large = 'large';

    /**
     * Too big or awkward to price automatically — engines, doors, panels. These
     * are not sold online at all; the part page falls back to reserve/enquire.
     */
    case QuoteOnly = 'quote_only';

    public function label(): string
    {
        return match ($this) {
            self::Small => 'Small (fits a courier bag)',
            self::Medium => 'Medium (boxed, one person lifts it)',
            self::Large => 'Large (bulky, still couriable)',
            self::QuoteOnly => 'Quote only — not sold online',
        };
    }

    public function shortLabel(): string
    {
        return match ($this) {
            self::Small => 'Small',
            self::Medium => 'Medium',
            self::Large => 'Large',
            self::QuoteOnly => 'Quote only',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Small => 'success',
            self::Medium => 'info',
            self::Large => 'warning',
            self::QuoteOnly => 'gray',
        };
    }

    /** Bands that can actually be bought and shipped online. */
    public static function shippable(): array
    {
        return [self::Small->value, self::Medium->value, self::Large->value];
    }

    public function isShippable(): bool
    {
        return $this !== self::QuoteOnly;
    }

    /** Freight cost for this band to the given region, in dollars. */
    public function rate(string $region): float
    {
        if (! $this->isShippable()) {
            return 0.0;
        }

        return (float) config("shipping.rates.{$this->value}.{$region}", 0);
    }

    public static function options(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }

        return $options;
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
