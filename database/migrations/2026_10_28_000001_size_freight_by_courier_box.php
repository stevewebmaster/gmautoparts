<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Small/Medium/Large become the actual NZ Couriers satchels and box sizes.
     *
     * Small meant "fits a courier bag", which is an E20 satchel, so it carries
     * over. Medium and Large have no honest equivalent — they spanned box sizes
     * whose South Island freight differs by over $200 — so those parts are left
     * unsized and drop out of the shop and the Google feed until the yard
     * measures them. Guessing again is how headlights ended up at $20 freight.
     *
     * Order lines also record what each part was charged for freight and what
     * to send it in, so the yard tickets it at the size the customer paid for.
     */
    public function up(): void
    {
        foreach (['parts', 'order_items'] as $table) {
            DB::table($table)->where('shipping_band', 'small')->update(['shipping_band' => 'e20']);
            DB::table($table)->whereIn('shipping_band', ['medium', 'large'])->update(['shipping_band' => null]);
        }

        Schema::table('order_items', function (Blueprint $table) {
            $table->decimal('shipping', 10, 2)->nullable()->after('shipping_band');
            $table->string('ship_as')->nullable()->after('shipping');
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['shipping', 'ship_as']);
        });

        $medium = ['box_015', 'e60', 'box_025', 'box_030', 'box_035'];
        $large = ['box_040', 'box_050', 'box_060', 'box_075', 'box_085', 'box_100', 'box_125'];

        foreach (['parts', 'order_items'] as $table) {
            DB::table($table)->where('shipping_band', 'e20')->update(['shipping_band' => 'small']);
            DB::table($table)->whereIn('shipping_band', $medium)->update(['shipping_band' => 'medium']);
            DB::table($table)->whereIn('shipping_band', $large)->update(['shipping_band' => 'large']);
        }
    }
};
