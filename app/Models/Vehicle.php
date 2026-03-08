<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehicle extends Model
{
    protected $fillable = [
        'make',
        'model',
        'year',
        'engine',
        'transmission',
        'stock_number',
        'notes',
        'images',
        'is_visible',
    ];

    protected $casts = [
        'images' => 'array',
        'is_visible' => 'boolean',
    ];

    public function parts(): HasMany
    {
        return $this->hasMany(Part::class, 'vehicle_id');
    }

    public function getDisplayNameAttribute(): string
    {
        return trim("{$this->year} {$this->make} {$this->model}");
    }
}
