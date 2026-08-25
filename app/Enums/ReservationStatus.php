<?php

namespace App\Enums;

enum ReservationStatus: string
{
    case Reserved = 'reserved';
    case Collected = 'collected';
    case Cancelled = 'cancelled';
    case Expired = 'expired';

    public function label(): string
    {
        return match ($this) {
            self::Reserved => 'Reserved',
            self::Collected => 'Collected',
            self::Cancelled => 'Cancelled',
            self::Expired => 'Expired',
        };
    }

    /** Statuses that still hold the part off the market. */
    public static function holding(): array
    {
        return [self::Reserved->value];
    }

    /** Filament badge colour. */
    public function color(): string
    {
        return match ($this) {
            self::Reserved => 'warning',
            self::Collected => 'success',
            self::Cancelled => 'gray',
            self::Expired => 'danger',
        };
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
