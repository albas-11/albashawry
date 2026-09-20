<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tryon_files', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('file_path');
            $table->string('preview_image')->nullable();

            $table->decimal('offset_x', 8, 2)->default(0);
            $table->decimal('offset_y', 8, 2)->default(0);
            $table->decimal('scale', 8, 2)->default(1);
            $table->decimal('rotation', 8, 2)->default(0);

            $table->json('calibration_data')->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tryon_files');
    }
};
