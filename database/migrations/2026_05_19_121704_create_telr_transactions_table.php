<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up(): void
{
    Schema::create('telr_transactions', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('order_id');
        $table->string('telr_order_ref')->unique();
        $table->string('cart_id')->nullable();
        $table->decimal('amount', 10, 2);
        $table->string('currency', 3)->default('SAR');
        $table->string('status')->default('pending');
        $table->longText('response_data')->nullable();
        $table->timestamps();

        $table->index('order_id');
    });
}

public function down(): void
{
    Schema::dropIfExists('telr_transactions');
}
};
