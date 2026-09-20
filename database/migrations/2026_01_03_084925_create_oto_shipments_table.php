<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('oto_shipments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->index();
            $table->string('oto_id')->unique()->nullable(); // OTO Shipment ID
            $table->string('shipment_id')->unique()->nullable(); // Carrier Tracking Number
            $table->string('order_code')->nullable();
            $table->string('delivery_option_id')->nullable();
            $table->string('carrier_name')->nullable();
            $table->decimal('weight', 8, 2);
            $table->decimal('shipping_cost', 10, 2)->nullable();
            $table->string('currency', 3)->default('MAD');
            $table->enum('status', [
                'pending', 'created', 'confirmed', 'picked_up',
                'in_transit', 'out_for_delivery', 'delivered',
                'cancelled', 'failed', 'returned'
            ])->default('pending');
            $table->string('label_url')->nullable();
            $table->text('tracking_url')->nullable();
            $table->text('destination_address')->nullable();
            $table->json('delivery_options')->nullable(); // Available carriers
            $table->text('response_data')->nullable();
            $table->timestamp('picked_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('oto_shipments');
    }
};
