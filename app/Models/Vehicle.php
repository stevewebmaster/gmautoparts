<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

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

    /**
     * The end of the vehicle's web address: "2016-jeep-grand-cherokee-16". The
     * name is for people and Google; the ID on the end is what finds the
     * vehicle, so renaming it never breaks a link (the old one redirects).
     */
    public function getUrlKeyAttribute(): string
    {
        return ltrim(Str::slug($this->display_name) . '-' . $this->id, '-');
    }

    /**
     * Find a vehicle from its web address. Only for the public route's
     * {vehicle:url_key}; the admin keeps looking vehicles up by plain ID.
     */
    public function resolveRouteBindingQuery($query, $value, $field = null)
    {
        if ($field !== 'url_key') {
            return parent::resolveRouteBindingQuery($query, $value, $field);
        }

        // The ID is the digits at the very end: "…-cherokee-16", or just "16"
        // from before names were added.
        $id = preg_match('/(?:^|-)(\d+)$/', (string) $value, $m) ? (int) $m[1] : 0;

        return $query->whereKey($id);
    }
}
