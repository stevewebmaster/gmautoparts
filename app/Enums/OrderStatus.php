<?php

namespace App\Enums;

enum OrderStatus: string
{
    /** Created, customer sent to Stripe, payment not yet confirmed. */
    case Pending = 'pending';

    case Paid = 'paid';
    case Dispatched = 'dispatched';
    case Collected = 'collected';
    case Cancelled = 'cancelled';
    case Refunded = 'refunded';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Awaiting payment',
            self::Paid => 'Paid',
            self::Dispatched => 'Dispatched',
            self::Collected => 'Collected',
            self::Cancelled => 'Cancelled',
            self::Refunded => 'Refunded',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Paid => 'success',
            self::Dispatched => 'info',
            self::Collected => 'info',
            self::Cancelled => 'gray',
            self::Refunded => 'danger',
        };
    }

    /** Statuses where the customer has paid and the parts are theirs. */
    public static function settled(): array
    {
        return [self::Paid->value, self::Dispatched->value, self::Collected->value];
    }

    /** Statuses that hold parts off the market. */
    public static function holding(): array
    {
        return [self::Pending->value, self::Paid->value, self::Dispatched->value, self::Collected->value];
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
