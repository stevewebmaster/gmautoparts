<?php

namespace Database\Seeders;

use App\Models\PartCategory;
use Illuminate\Database\Seeder;

class PartCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'NOW DISMANTLING', 'slug' => 'now-dismantling', 'sort_order' => 1],
            ['name' => 'ENGINE', 'slug' => 'engine', 'sort_order' => 2],
            ['name' => 'GEARBOX', 'slug' => 'gearbox', 'sort_order' => 3],
            ['name' => 'DIFFERENTIAL', 'slug' => 'differential', 'sort_order' => 4],
            ['name' => 'BODYPANELS & FRONT PARTS', 'slug' => 'bodypanels-front-parts', 'sort_order' => 5],
            ['name' => 'REAR BODY PARTS & PANELS', 'slug' => 'rear-body-parts-panels', 'sort_order' => 6],
            ['name' => 'DOOR', 'slug' => 'door', 'sort_order' => 7],
            ['name' => 'DASH', 'slug' => 'dash', 'sort_order' => 8],
            ['name' => 'ELECTRICAL', 'slug' => 'electrical', 'sort_order' => 9],
            ['name' => 'STEERING', 'slug' => 'steering', 'sort_order' => 10],
            ['name' => 'FRONT SUSPENSION', 'slug' => 'front-suspension', 'sort_order' => 11],
            ['name' => 'REAR SUSPENSION', 'slug' => 'rear-suspension', 'sort_order' => 12],
            ['name' => 'INTERIOR', 'slug' => 'interior', 'sort_order' => 13],
            ['name' => 'ENGINE COOLING', 'slug' => 'engine-cooling', 'sort_order' => 14],
            ['name' => 'EXHAUST SYSTEM', 'slug' => 'exhaust-system', 'sort_order' => 15],
            ['name' => 'FUEL DELIVERY', 'slug' => 'fuel-delivery', 'sort_order' => 16],
            ['name' => 'BRAKES', 'slug' => 'brakes', 'sort_order' => 17],
            ['name' => 'HEAT & A/C', 'slug' => 'heat-ac', 'sort_order' => 18],
            ['name' => 'LIGHTS', 'slug' => 'lights', 'sort_order' => 19],
            ['name' => 'SPECIALS', 'slug' => 'specials', 'sort_order' => 20],
            ['name' => 'WHEELS', 'slug' => 'wheels', 'sort_order' => 21],
        ];

        foreach ($categories as $cat) {
            PartCategory::updateOrCreate(
                ['slug' => $cat['slug']],
                array_merge($cat, ['is_active' => true])
            );
        }
    }
}
