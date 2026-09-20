<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            // Registre de commerce (السجل التجاري)
            $table->string('cr_number')->nullable()->after('address');
            $table->string('cr_image')->nullable()->after('cr_number');

            // Numéro de TVA (الرقم الضريبي)
            $table->string('vat_number')->nullable()->after('cr_image');
            $table->string('vat_image')->nullable()->after('vat_number');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['cr_number', 'cr_image', 'vat_number', 'vat_image']);
        });
    }
};
