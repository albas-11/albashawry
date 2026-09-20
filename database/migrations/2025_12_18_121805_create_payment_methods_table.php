<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique(); // 'stripe', 'paypal', 'cod'
            $table->json('display_name'); // Translatable names
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('configuration')->nullable(); // Store API keys, settings
            $table->string('icon')->nullable(); // Icon path
            $table->integer('sort_order')->default(0);
            $table->decimal('min_amount', 10, 2)->nullable();
            $table->decimal('max_amount', 10, 2)->nullable();
            $table->json('allowed_currencies')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
