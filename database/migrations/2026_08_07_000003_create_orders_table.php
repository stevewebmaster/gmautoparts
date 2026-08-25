<?php

use App\Enums\OrderStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->string('status')->default(OrderStatus::Pending->value)->index();

            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();

            // 'pickup' or 'delivery'.
            $table->string('fulfilment')->default('delivery');
            $table->string('address_line1')->nullable();
            $table->string('address_line2')->nullable();
            $table->string('suburb')->nullable();
            $table->string('city')->nullable();
            $table->string('postcode')->nullable();
            $table->string('region')->nullable();
            $table->boolean('is_rural')->default(false);
            $table->text('notes')->nullable();

            // Money is stored as it was charged. Part prices and freight rates
            // both change; an order must never silently re-price itself.
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('shipping', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->string('currency', 3)->default('NZD');

            $table->string('stripe_session_id')->nullable()->index();
            $table->string('stripe_payment_intent')->nullable()->index();

            $table->timestamp('paid_at')->nullable();
            $table->timestamp('dispatched_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            // Pending orders hold their parts; this is when that hold lapses.
            $table->timestamp('expires_at')->nullable()->index();

            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('part_id')->nullable()->constrained()->nullOnDelete();

            // Snapshot: the part can be renamed, repriced or deleted later.
            $table->string('title');
            $table->decimal('price', 10, 2);
            $table->unsignedInteger('quantity')->default(1);
            $table->string('shipping_band')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
