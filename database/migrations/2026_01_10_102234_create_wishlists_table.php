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
        Schema::create('wishlists', function (Blueprint $table) {
            $table->id();

            // Lien avec l'utilisateur
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Id générique et type (polymorphe)
            $table->unsignedBigInteger('item_id');
            $table->string('item_type'); // product, contact_lens, accessory

            $table->timestamps();

            // Unique pour éviter doublons
            $table->unique(['user_id', 'item_id', 'item_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wishlists');
    }
};
