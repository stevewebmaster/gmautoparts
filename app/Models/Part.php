<?php

namespace App\Models;

use App\Enums\PartStatus;
use App\Enums\ShippingBand;
use App\Services\GoogleProductFeed;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class Part extends Model
{
    protected static function booted(): void
    {
        static::saving(function (Part $part) {
            if (empty($part->slug) && !empty($part->title)) {
                $part->slug = Str::slug($part->title);
            }

            // Keep sold_at in step with status, so channel feeds and reporting
            // have a reliable "when" without the caller having to remember it.
            if ($part->status === PartStatus::Sold) {
                $part->sold_at ??= now();
            } else {
                $part->sold_at = null;
            }
        });

        // Keep the Google feed honest: marking a part sold should drop it from
        // the next fetch rather than waiting out the cache TTL.
        $flushFeed = fn () => Cache::forget(GoogleProductFeed::CACHE_KEY);

        static::saved($flushFeed);
        static::deleted($flushFeed);
    }
    protected $fillable = [
        'title',
        'slug',
        'part_category_id',
        'part_subcategory_id',
        'make',
        'model',
        'year',
        'stock_number',
        'description',
        'price',
        'condition',
        'status',
        'quantity',
        'shipping_band',
        'images',
        'vehicle_id',
        'is_visible',
        'is_featured',
    ];

    /**
     * Mirrors the database defaults so a freshly instantiated Part already has
     * a status — otherwise $part->status is null until the row is re-read.
     */
    protected $attributes = [
        'status' => PartStatus::Available->value,
        'quantity' => 1,
    ];

    protected $casts = [
        'images' => 'array',
        'price' => 'decimal:2',
        'status' => PartStatus::class,
        'shipping_band' => ShippingBand::class,
        'quantity' => 'integer',
        'sold_at' => 'datetime',
        'is_visible' => 'boolean',
        'is_featured' => 'boolean',
    ];

    /** Shown on the site at all. */
    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('is_visible', true);
    }

    /** Appears in the public parts listing and filter facets. */
    public function scopeListable(Builder $query): Builder
    {
        return $query->visible()->whereIn('status', PartStatus::listable());
    }

    public function isSold(): bool
    {
        return $this->status === PartStatus::Sold;
    }

    public function isWithdrawn(): bool
    {
        return $this->status === PartStatus::Withdrawn;
    }

    /**
     * Whether the part can be bought online. Needs a price and a real shipping
     * band on top of being available — a part with no band set cannot have its
     * freight priced, so it is not sold online at all.
     */
    public function isPurchasable(): bool
    {
        return $this->isReservable()
            && $this->price > 0
            && $this->shipping_band?->isShippable() === true;
    }

    /**
     * Eligible for the Google Merchant feed: priced and banded, so the checkout
     * Google requires actually works for it. On-hold parts stay in (as
     * out_of_stock) to keep the listing alive; quote-only parts do not, because
     * they cannot be bought online at all.
     */
    public function scopeFeedable(Builder $query): Builder
    {
        return $query->listable()
            ->whereNotNull('price')
            ->where('price', '>', 0)
            ->whereIn('shipping_band', ShippingBand::shippable());
    }

    /** In the shop: available, priced and banded. */
    public function scopePurchasable(Builder $query): Builder
    {
        return $query->visible()
            ->where('status', PartStatus::Available)
            ->whereNotNull('price')
            ->where('price', '>', 0)
            ->whereIn('shipping_band', ShippingBand::shippable());
    }

    /**
     * Whether the part can be reserved. Stricter than isEnquirable(): a part
     * already on hold for someone else must not be reservable a second time.
     */
    public function isReservable(): bool
    {
        return $this->is_visible && $this->status === PartStatus::Available;
    }

    /** Whether the part can still be enquired about. */
    public function isEnquirable(): bool
    {
        return $this->is_visible
            && in_array($this->status?->value, PartStatus::listable(), true);
    }

    public function markSold(): bool
    {
        return $this->update(['status' => PartStatus::Sold]);
    }

    public function markAvailable(): bool
    {
        return $this->update(['status' => PartStatus::Available]);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(PartCategory::class, 'part_category_id');
    }

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(PartSubcategory::class, 'part_subcategory_id');
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }
}
