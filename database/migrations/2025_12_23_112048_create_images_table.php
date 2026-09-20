<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('images', function (Blueprint $table) {
            $table->id();

            $table->morphs('imageable');
            // reviewable_id: on ecrie id=5
            // reviewable_type: on ecrie App\Models\ContactLens ou App\Models\Product

            $table->string('image');
            $table->boolean('is_primary')->default(false);
            $table->integer('order')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('images');
    }
};
