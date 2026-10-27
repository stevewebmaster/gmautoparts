<?php

namespace App\Models;

use App\Enums\ShippingBand;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = ['order_id', 'part_id', 'title', 'price', 'quantity', 'shipping_band', 'shipping', 'ship_as'];

    protected $casts = [
        'price' => 'decimal:2',
        'quantity' => 'integer',
        'shipping_band' => ShippingBand::class,
        'shipping' => 'decimal:2',
        // What the freight was priced as — may be bigger than the part's own
        // size, when that is cheaper to the customer's island.
        'ship_as' => ShippingBand::class,
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function part(): BelongsTo
    {
        return $this->belongsTo(Part::class);
    }

    public function lineTotal(): float
    {
        return (float) $this->price * $this->quantity;
    }
}
