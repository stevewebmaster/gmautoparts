<?php

namespace Database\Seeders;

use App\Models\PartCategory;
use App\Models\PartSubcategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PartSubcategorySeeder extends Seeder
{
    /**
     * Full list of part categories and subcategories from the legacy website.
     * Re-run with: php artisan db:seed --class=PartSubcategorySeeder
     */
    public function run(): void
    {
        $data = [
            'now-dismantling' => [
                'CHRYSLER', 'DAIHATSU', 'DODGE', 'FORD', 'HOLDEN', 'HYUNDAI', 'SUZUKI',
            ],
            'engine' => [
                'Cylinder Block', 'Cylinder Head', 'EGR Valve', 'Engine Assemblies', 'Engine Conversion',
                'Exhaust Manifold', 'Front Cover', 'Intake Manifold', 'Oil Pump', 'Sump',
                'SuperCharger', 'Turbo Charger',
            ],
            'gearbox' => [
                'Electronic Control Unit', 'Shifter', 'Transfer Case', 'Transmission Automatic', 'Transmission Manual',
            ],
            'differential' => [
                'Diff Heads', 'Doughnuts', 'DriveAxle LH', 'DriveAxle RH', 'Driveshaft',
                'HandBrake Cable', 'Rear Assemblies',
            ],
            'bodypanels-front-parts' => [
                'Bonnet Lock Support', 'Front Bumper', 'Grille', 'Radiator Support Panel', 'Washer Bottle',
                'Bonnet', 'Front 1/2 Assembly', 'NoseCut Assembly', 'Guard RH', 'Guard LH',
            ],
            'rear-body-parts-panels' => [
                'Bootlid', 'Bumper Bar', 'Fuel Flap', 'Quater LH', 'Quater RH',
                'Rear Half Assemblies', 'Roof Cut', 'TailGate',
            ],
            'door' => [
                'Central Lock Solenoid', 'Check Strap', 'Door Interior Panels', 'Door LHF', 'Door LHR',
                'Door Panel Speaker Covers', 'Door RHF', 'Door RHR', 'Glass LF', 'Glass LR',
                'Glass RF', 'Glass RR', 'Handle Exterior', 'Mirror LH', 'Mirror RH',
                'Window Regulator LF', 'Window Regulator LR', 'Window Regulator RF', 'Window Regulator RR',
            ],
            'dash' => [
                'Air Bag Clock Spring', 'Airbag LH', 'Airbag Module', 'Airbag RH', 'Dash Assembly',
                'GloveBox', 'Guages', 'Instrument Cluster', 'Steering Column', 'Steering Column Shroud',
                'Steering Wheel', "Surrounds/Facia's",
            ],
            'electrical' => [
                'Alternator', 'Body Control Module', 'Coil', 'Coil Pack', 'Crank & Cam Sensor',
                'Distributor', 'ECU', 'Fuse Box', 'Headlight/Indicator/Wiper Switches', 'Ignition Module',
                'Starter Motor', 'Stereo', 'Stereo Control', 'Switches', 'Window Door Switch',
                'Window Master Switch', 'Wiper Motor & Mechanism',
            ],
            'steering' => [
                'Steering Pump', 'Steering Rack',
            ],
            'front-suspension' => [
                'Lower Arm LF', 'Lower Arm RF',
            ],
            'rear-suspension' => [
                'Control Arm LH', 'Control Arm RH', 'Hub LH', 'Hub RH', 'Lower Control Arm LH',
                'Lower Control Arm RH', 'Rear Diff Cradle', 'Self Level Pump', 'Upper Control Arm LH', 'Upper Control Arm RH',
            ],
            'interior' => [
                'Carpet', 'Centre Console', 'Hoodlining', 'Seat Trim', 'Seats',
            ],
            'engine-cooling' => [
                'Fan', 'Header Tank', 'Heater Tap', 'Over Flow Bottle', 'Radiator', 'Shroud', 'Water Pump',
            ],
            'exhaust-system' => [
                // No subcategories on legacy list; leave empty so admin can add later if needed
            ],
            'fuel-delivery' => [
                'Air Flow Metre', 'Tank', 'Throttle Body',
            ],
            'brakes' => [
                'ABS Control Units',
            ],
            'heat-ac' => [
                'Air Conditioning Pump', 'Heater Control Panels', 'Heater Fan Motor', 'Heater Resistor',
            ],
            'lights' => [
                'Driving Lights', 'Guard Repeater Lights', 'LF Headlight', 'LR Tail Lamp', 'RF Headlight', 'RR Tail Lamp',
            ],
            'specials' => [
                // No subcategories on legacy list
            ],
            'wheels' => [
                // No subcategories on legacy list
            ],
        ];

        foreach ($data as $categorySlug => $subNames) {
            $category = PartCategory::where('slug', $categorySlug)->first();
            if (!$category) {
                continue;
            }
            foreach ($subNames as $i => $name) {
                $slug = Str::slug($name);
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
