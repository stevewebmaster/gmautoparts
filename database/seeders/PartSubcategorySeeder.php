<?php

namespace Database\Seeders;

use App\Models\PartCategory;
use App\Models\PartSubcategory;
use Illuminate\Database\Seeder;

class PartSubcategorySeeder extends Seeder
{
    /**
     * Subcategories are CMS-managed. This seeder adds a few examples
     * so the structure is clear; clients add the rest via Admin.
     */
    public function run(): void
    {
        $examples = [
            'engine' => ['Engine Block', 'Cylinder Head', 'Turbo', 'Alternator'],
            'electrical' => ['ECU', 'Starter Motor', 'Wiring Loom'],
            'brakes' => ['Brake Caliper', 'Brake Disc', 'Brake Pad Set'],
        ];

        foreach ($examples as $categorySlug => $subNames) {
            $category = PartCategory::where('slug', $categorySlug)->first();
            if (!$category) {
                continue;
            }
            foreach ($subNames as $i => $name) {
                $slug = \Illuminate\Support\Str::slug($name);
                PartSubcategory::updateOrCreate(
                    [
                        'part_category_id' => $category->id,
                        'slug' => $slug,
                    ],
                    [
                        'name' => $name,
                        'sort_order' => $i,
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}
