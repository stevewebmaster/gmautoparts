<?php

namespace App\Enums;

use Illuminate\Database\Eloquent\Builder;

/**
 * Why a part is, or is not, in the Google Merchant feed.
 *
 * The cases partition the parts table: every part matches exactly one, so the
 * counts always add up to the catalogue total. That is the whole point — the
 * client needs to be able to trust "12 in the feed, 40 not, here is why".
 *
 * Both the dashboard counts and the Parts-list filter run through
 * {@see self::constrain()}, so a number on the dashboard and the rows behind it
 * can never drift apart.
 */
enum FeedStatus: string
{
    case InFeed = 'in_feed';

    case Hidden = 'hidden';
    case NotListable = 'not_listable';
    case NoPhoto = 'no_photo';
    case NoPrice = 'no_price';
    case NoBand = 'no_band';
    case QuoteOnly = 'quote_only';

    public function label(): string
    {
        return match ($this) {
            self::InFeed => 'In the Google feed',
            self::Hidden => 'Hidden from the site',
            self::NotListable => 'Sold or withdrawn',
            self::NoPhoto => 'No photo',
            self::NoPrice => 'No price',
            self::NoBand => 'No shipping band',
            self::QuoteOnly => 'Quote only',
        };
    }

    /** Plain-English why, written for the client rather than for us. */
    public function description(): string
    {
        return match ($this) {
            self::InFeed => 'Sent to Google Merchant Center on the next fetch.',
            self::Hidden => 'Not on the website at all, so not sent to Google. Nothing to fix unless this was a mistake.',
            self::NotListable => 'Sold parts and withdrawn parts are dropped from the feed. This is correct — a sold wrecker part is not coming back.',
            self::NoPhoto => 'Google will not list a product without an image. Add at least one photo.',
            self::NoPrice => 'Google rejects a missing or zero price. Add a price.',
            self::NoBand => 'Without a shipping band the freight cannot be priced, so the part cannot be bought online and Google will not take it. Set a band.',
            self::QuoteOnly => 'Too big to price freight for automatically, so it is not sold online and not sent to Google. Deliberate — change the band if that is wrong.',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::InFeed => 'success',
            self::NoPhoto, self::NoPrice, self::NoBand => 'warning',
            self::Hidden, self::NotListable, self::QuoteOnly => 'gray',
        };
    }

    /**
     * Whether this is a part that ought to be in the feed and is only missing
     * something. These are the worklist; the rest are deliberate choices.
     */
    public function isFixable(): bool
    {
        return match ($this) {
            self::NoPhoto, self::NoPrice, self::NoBand => true,
            default => false,
        };
    }

    /**
     * Narrow a Part query to this case only.
     *
     * The cases are tested in a fixed order — hidden, then sold/withdrawn, then
     * photo, price, band — and each case re-states the conditions of the ones
     * above it so that no part can fall into two buckets.
     */
    public function constrain(Builder $query): Builder
    {
        return match ($this) {
            self::Hidden => $query->where('is_visible', false),

            self::NotListable => $query
                ->where('is_visible', true)
                ->whereNotIn('status', PartStatus::listable()),

            self::NoPhoto => $query->listable()->withoutImages(),

            self::NoPrice => $query->listable()->withImages()
                ->where(fn (Builder $inner) => $inner->whereNull('price')->orWhere('price', '<=', 0)),

            self::NoBand => $query->listable()->withImages()
                ->whereNotNull('price')->where('price', '>', 0)
                ->whereNull('shipping_band'),

            self::QuoteOnly => $query->listable()->withImages()
                ->whereNotNull('price')->where('price', '>', 0)
                ->where('shipping_band', ShippingBand::QuoteOnly->value),

            // Deliberately the same scope the feed itself builds from, plus the
            // image check the feed applies in PHP.
            self::InFeed => $query->feedable()->withImages(),
        };
    }

    /** The excluded cases, in the order they are worth working through. */
    public static function excluded(): array
    {
        return [self::NoPhoto, self::NoPrice, self::NoBand, self::QuoteOnly, self::Hidden, self::NotListable];
    }

    public static function options(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }

        return $options;
    }
}
