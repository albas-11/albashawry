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
        Schema::create('pixels', function (Blueprint $table) {
            $table->id();
            $table->string('provider')->comment("platform dyal ads => facebook | tiktok | google...");
            $table->string('pixel_id');
            $table->string('name')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('events')->nullable()->comment('bhal => "pageview","add_to_cart","purchase"');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pixels');
    }
};
