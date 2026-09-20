<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('accessories', function (Blueprint $table) {
            // Supprime d'abord la contrainte de clé étrangère
            $table->dropForeign(['brand_id']);

            // Puis supprime la colonne
            $table->dropColumn('brand_id');
        });
    }

    public function down(): void
    {
        Schema::table('accessories', function (Blueprint $table) {
            $table->unsignedBigInteger('brand_id')->nullable();

            // Si tu veux rollback, tu peux recréer la clé étrangère
            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('cascade');
        });
    }
};
