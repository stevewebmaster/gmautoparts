<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Part extends Model
{
    protected static function booted(): void
    {
        static::saving(function (Part $part) {
            if (empty($part->slug) && !empty($part->title)) {
                $part->slug = Str::slug($part->title);
            }
        });
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
        'images',
        'vehicle_id',
        'is_visible',
        'is_featured',
    ];

    protected $casts = [
        'images' => 'array',
        'price' => 'decimal:2',
        'is_visible' => 'boolean',
        'is_featured' => 'boolean',
    ];

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
