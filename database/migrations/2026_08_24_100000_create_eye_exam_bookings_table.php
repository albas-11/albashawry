<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('eye_exam_bookings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->foreignId('order_item_id')->nullable()->constrained('order_items')->nullOnDelete();
            $table->foreignId('store_id')->nullable()->constrained('stores')->nullOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();

            $table->string('product_name')->nullable();
            $table->string('customer_name');
            $table->string('customer_phone');

            // book_eye_test | store_measurement
            $table->string('flow')->default('book_eye_test');

            $table->date('booking_date');
            $table->string('booking_time');

            $table->string('store_name')->nullable();

            $table->enum('status', ['pending', 'confirmed', 'done', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['booking_date', 'booking_time']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('eye_exam_bookings');
    }
};
