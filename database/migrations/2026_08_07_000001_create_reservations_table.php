<?php

use App\Enums\ReservationStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Reserve-for-collection orders. Google requires a real purchase path before
     * it will show products — an enquiry form is not enough — and accepts
     * "payment upon collection", which is what this records.
     */
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('part_id')->nullable()->constrained()->nullOnDelete();

            // Snapshot of what was actually reserved. The part can be renamed,
            // repriced or deleted afterwards; the order must not change.
            $table->string('part_title');
            $table->decimal('part_price', 10, 2)->nullable();

            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->text('notes')->nullable();

            $table->string('status')->default(ReservationStatus::Reserved->value)->index();
            $table->timestamp('expires_at')->nullable()->index();
            $table->timestamp('collected_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
