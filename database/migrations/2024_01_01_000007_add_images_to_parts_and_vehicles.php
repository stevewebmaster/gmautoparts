<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('parts', function (Blueprint $table) {
            $table->json('images')->nullable()->after('condition');
        });

        Schema::table('vehicles', function (Blueprint $table) {
            $table->json('images')->nullable()->after('notes');
        });
    }

    public function down(): void
    {
        Schema::table('parts', function (Blueprint $table) {
            $table->dropColumn('images');
        });
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn('images');
        });
    }
};
