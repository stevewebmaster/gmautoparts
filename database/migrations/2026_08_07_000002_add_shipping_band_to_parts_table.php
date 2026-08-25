<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Size band used to price freight. Deliberately nullable with no default:
     * a part with no band set is treated as quote-only and is not sold online,
     * so existing stock cannot start shipping at a guessed rate the moment this
     * deploys. G&M band parts as they work through them.
     */
    public function up(): void
    {
        Schema::table('parts', function (Blueprint $table) {
            $table->string('shipping_band')->nullable()->index()->after('quantity');
        });
    }

    public function down(): void
    {
        Schema::table('parts', function (Blueprint $table) {
            $table->dropIndex(['shipping_band']);
            $table->dropColumn('shipping_band');
        });
    }
};
