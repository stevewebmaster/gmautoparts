<?php

namespace App\Enums;

enum PartStatus: string
{
    case Available = 'available';
    case OnHold = 'on_hold';
    case Sold = 'sold';
    case Withdrawn = 'withdrawn';

    public function label(): string
    {
        return match ($this) {
            self::Available => 'Available',
            self::OnHold => 'On hold',
            self::Sold => 'Sold',
            self::Withdrawn => 'Withdrawn',
        };
    }

    /**
     * Statuses that still appear in the public parts listing. Sold parts keep
     * their detail page (so existing links and search results stay useful) but
     * drop out of the grid; withdrawn parts disappear entirely.
     */
    public static function listable(): array
    {
        return [self::Available->value, self::OnHold->value];
    }

    /** Filament badge colour. */
    public function color(): string
    {
        return match ($this) {
            self::Available => 'success',
            self::OnHold => 'warning',
            self::Sold => 'danger',
            self::Withdrawn => 'gray',
        };
    }

    /** Value => label map for select inputs. */
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
