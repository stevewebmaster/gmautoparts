<?php

use App\Enums\PartStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Inventory state for parts, so the site is the single source of truth once
     * listings are pushed to external channels (Google Shopping, Trade Me).
     *
     * `status` is a plain string rather than a MySQL enum so local SQLite and
     * production MySQL behave identically and new states can be added without
     * an ALTER. It stays orthogonal to `is_visible`: `is_visible` controls
     * whether a part appears on the site at all, `status` controls whether it
     * can still be bought.
     */
    public function up(): void
    {
        Schema::table('parts', function (Blueprint $table) {
            $table->string('status')->default(PartStatus::Available->value)->index()->after('condition');
            $table->unsignedInteger('quantity')->default(1)->after('status');
            $table->timestamp('sold_at')->nullable()->after('quantity');
        });
    }

    public function down(): void
    {
        Schema::table('parts', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropColumn(['status', 'quantity', 'sold_at']);
        });
    }
};
