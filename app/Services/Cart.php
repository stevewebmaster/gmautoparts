<?php

namespace App\Services;

use App\Models\Part;
use Illuminate\Support\Collection;

/**
 * Session-backed cart.
 *
 * Only part ids are stored. Everything else — price, availability, band — is
 * read fresh from the database on every access, so a cart left open overnight
 * cannot check out at yesterday's price or buy something already sold.
 *
 * Used parts are one-offs, so quantity is always 1 and adding twice is a no-op.
 */
class Cart
{
    protected const SESSION_KEY = 'cart.part_ids';

    /** Parts still in the cart and still purchasable. */
    public function items(): Collection
    {
        $ids = $this->ids();

        if (empty($ids)) {
            return collect();
        }

        $parts = Part::whereIn('id', $ids)->with(['category'])->get()->keyBy('id');

        // Preserve the order things were added in.
        return collect($ids)
            ->map(fn ($id) => $parts->get($id))
            ->filter()
            ->values();
    }

    /** Items that can no longer be bought, so checkout can say why. */
    public function unavailable(): Collection
    {
        return $this->items()->reject(fn (Part $part) => $part->isPurchasable())->values();
    }

    public function purchasable(): Collection
    {
        return $this->items()->filter(fn (Part $part) => $part->isPurchasable())->values();
    }

    public function add(Part $part): void
    {
        $ids = $this->ids();

        if (! in_array($part->id, $ids, true)) {
            $ids[] = $part->id;
            $this->put($ids);
        }
    }

    public function remove(int $partId): void
    {
        $this->put(array_values(array_filter($this->ids(), fn ($id) => $id !== $partId)));
    }

    public function has(int $partId): bool
    {
        return in_array($partId, $this->ids(), true);
    }

    public function clear(): void
    {
        session()->forget(self::SESSION_KEY);
    }

    public function count(): int
    {
        return count($this->ids());
    }

    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }

    public function subtotal(): float
    {
        return (float) $this->purchasable()->sum(fn (Part $part) => (float) $part->price);
    }

    /**
     * Freight for the whole cart.
     *
     * Each part is charged its own freight. NZ Couriers charge per item even
     * within one consignment, so charging only the biggest part — as this
     * once did — lost the full price of every other box. A pair of headlights
     * to Christchurch is two boxes and two charges. When the yard can pack
     * parts together for less, it refunds the difference.
     */
    public function shipping(string $fulfilment, ?string $region = null, bool $isRural = false): float
    {
        if ($fulfilment === 'pickup') {
            return 0.0;
        }

        $items = $this->purchasable();

        if ($items->isEmpty() || ! $region) {
            return 0.0;
        }

        $freeOver = config('shipping.free_over');

        if ($freeOver !== null && $this->subtotal() >= (float) $freeOver) {
            return 0.0;
        }

        $rate = (float) $items->sum(fn (Part $part) => $part->shipping_band?->rate($region) ?? 0.0);

        // NZ Couriers need a rural ticket on every item, so it is per part too.
        if ($isRural) {
            $rate += $items->count() * (float) config('shipping.rural_surcharge', 0);
        }

        return round($rate, 2);
    }

    public function total(string $fulfilment, ?string $region = null, bool $isRural = false): float
    {
        return round($this->subtotal() + $this->shipping($fulfilment, $region, $isRural), 2);
    }

    /**
     * Delivery freight to each region, urban and rural, as the server will
     * charge it. Lets the checkout page preview freight without repeating the
     * rate rules in JavaScript.
     */
    public function freightByRegion(): array
    {
        $freight = [];

        foreach (array_keys(config('shipping.regions')) as $region) {
            $freight[$region] = [
                'urban' => $this->shipping('delivery', $region),
                'rural' => $this->shipping('delivery', $region, true),
            ];
        }

        return $freight;
    }

    protected function ids(): array
    {
        return array_values(array_unique(array_map('intval', (array) session(self::SESSION_KEY, []))));
    }

    protected function put(array $ids): void
    {
        session([self::SESSION_KEY => array_values($ids)]);
    }
}
