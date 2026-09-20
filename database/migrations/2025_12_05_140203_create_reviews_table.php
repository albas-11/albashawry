<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();

            // Morph pour les reviews "polymorphiques"
            $table->morphs('reviewable');

            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('set null');

            $table->integer('rating'); // note de 1 à 5
            $table->text('title')->nullable();
            $table->text('comment');
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_approved')->default(false);

            $table->timestamps();

            // Un utilisateur ne peut reviewer qu'une seule fois le même item
            $table->unique(['reviewable_id', 'reviewable_type', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
