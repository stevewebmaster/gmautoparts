<?php

namespace App\Models;

use App\Enums\PartStatus;
use App\Enums\ReservationStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Reservation extends Model
{
    /** Days a reservation holds a part before it is released back to available. */
    public const HOLD_DAYS = 7;

    protected $fillable = [
        'reference',
        'part_id',
        'part_title',
        'part_price',
        'name',
        'email',
        'phone',
        'notes',
        'status',
        'expires_at',
        'collected_at',
        'cancelled_at',
    ];

    protected $attributes = [
        'status' => ReservationStatus::Reserved->value,
    ];

    protected $casts = [
        'part_price' => 'decimal:2',
        'status' => ReservationStatus::class,
        'expires_at' => 'datetime',
        'collected_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function part(): BelongsTo
    {
        return $this->belongsTo(Part::class);
    }

    /**
     * Reserves a part: snapshots it, holds it off the market, and sets the
     * collection deadline. The caller is responsible for sending the emails.
     */
    public static function reserve(Part $part, array $details): self
    {
        $reservation = static::create([
            'reference' => static::generateReference(),
            'part_id' => $part->id,
            'part_title' => $part->title,
            'part_price' => $part->price,
            'name' => $details['name'],
            'email' => $details['email'],
            'phone' => $details['phone'] ?? null,
            'notes' => $details['notes'] ?? null,
            'expires_at' => now()->addDays(static::HOLD_DAYS),
        ]);

        $part->update(['status' => PartStatus::OnHold]);

        return $reservation;
    }

    /**
     * Human-readable and unambiguous when read aloud over the phone: no O/0 or
     * I/1 confusion.
     */
    public static function generateReference(): string
    {
        do {
            $reference = 'GM-' . Str::upper(Str::random(6));
            $reference = str_replace(['O', 'I', '0', '1'], ['P', 'J', '2', '3'], $reference);
        } while (static::where('reference', $reference)->exists());

        return $reference;
    }

    public function isHolding(): bool
    {
        return in_array($this->status?->value, ReservationStatus::holding(), true);
    }

    public function markCollected(): bool
    {
        $this->part?->update(['status' => PartStatus::Sold]);

        return $this->update([
            'status' => ReservationStatus::Collected,
            'collected_at' => now(),
        ]);
    }

    public function cancel(): bool
    {
        $this->releasePart();

        return $this->update([
            'status' => ReservationStatus::Cancelled,
            'cancelled_at' => now(),
        ]);
    }

    public function expire(): bool
    {
        $this->releasePart();

        return $this->update(['status' => ReservationStatus::Expired]);
    }

    /**
     * Puts the part back on the market, but only if this reservation is what is
     * holding it — a part manually marked sold must not be resurrected by a
     * stale reservation expiring.
     */
    protected function releasePart(): void
    {
        if ($this->part?->status === PartStatus::OnHold) {
            $this->part->update(['status' => PartStatus::Available]);
        }
    }

    public function scopeHolding(Builder $query): Builder
    {
        return $query->whereIn('status', ReservationStatus::holding());
    }

    public function scopeDue(Builder $query): Builder
    {
        return $query->holding()->whereNotNull('expires_at')->where('expires_at', '<=', now());
    }
}
