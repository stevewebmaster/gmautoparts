<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PartCategory extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function subcategories(): HasMany
    {
        return $this->hasMany(PartSubcategory::class, 'part_category_id')->orderBy('sort_order');
    }

    public function parts(): HasMany
    {
        return $this->hasMany(Part::class, 'part_category_id');
    }
}
