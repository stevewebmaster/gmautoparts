<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Enums\PartStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Order extends Model
{
    /** How long an unpaid order holds its parts before they go back on sale. */
    public const PENDING_HOLD_MINUTES = 60;

    protected $fillable = [
        'reference', 'status', 'name', 'email', 'phone',
        'fulfilment', 'address_line1', 'address_line2', 'suburb', 'city',
        'postcode', 'region', 'is_rural', 'notes',
        'subtotal', 'shipping', 'total', 'currency',
        'stripe_session_id', 'stripe_payment_intent',
        'paid_at', 'dispatched_at', 'cancelled_at', 'expires_at',
    ];

    protected $attributes = [
        'status' => OrderStatus::Pending->value,
    ];

    protected $casts = [
        'status' => OrderStatus::class,
        'is_rural' => 'boolean',
        'subtotal' => 'decimal:2',
        'shipping' => 'decimal:2',
        'total' => 'decimal:2',
        'paid_at' => 'datetime',
        'dispatched_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function isPickup(): bool
    {
        return $this->fulfilment === 'pickup';
    }

    public function isPaid(): bool
    {
        return in_array($this->status?->value, OrderStatus::settled(), true);
    }

    /** Unambiguous read aloud: no O/I/0/1. */
    public static function generateReference(): string
    {
        do {
            $reference = 'GM' . now()->format('y') . '-' . Str::upper(Str::random(5));
            $reference = str_replace(['O', 'I', '0', '1'], ['P', 'J', '2', '3'], $reference);
        } while (static::where('reference', $reference)->exists());

        return $reference;
    }

    /**
     * Confirms payment: records it and sells the parts. Idempotent, because
     * Stripe can deliver the same webhook more than once and the success page
     * may also confirm it first.
     */
    public function markPaid(?string $paymentIntent = null): bool
    {
        if ($this->isPaid()) {
            return false;
        }

        $this->update([
            'status' => OrderStatus::Paid,
            'paid_at' => now(),
            'stripe_payment_intent' => $paymentIntent ?: $this->stripe_payment_intent,
            'expires_at' => null,
        ]);

        foreach ($this->items as $item) {
            $item->part?->update(['status' => PartStatus::Sold]);
        }

        return true;
    }

    /**
     * Abandons an unpaid order and puts its parts back on sale. Only releases a
     * part still sitting at on_hold, so a part sold in the yard meanwhile is
     * never resurrected.
     */
    public function release(OrderStatus $status = OrderStatus::Cancelled): bool
    {
        if ($this->isPaid()) {
            return false;
        }

        foreach ($this->items as $item) {
            if ($item->part?->status === PartStatus::OnHold) {
                $item->part->update(['status' => PartStatus::Available]);
            }
        }

        return $this->update([
            'status' => $status,
            'cancelled_at' => now(),
            'expires_at' => null,
        ]);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', OrderStatus::Pending);
    }

    /** Unpaid orders whose hold has lapsed. */
    public function scopeStale(Builder $query): Builder
    {
        return $query->pending()->whereNotNull('expires_at')->where('expires_at', '<=', now());
    }

    public function scopeSettled(Builder $query): Builder
    {
        return $query->whereIn('status', OrderStatus::settled());
    }
}
