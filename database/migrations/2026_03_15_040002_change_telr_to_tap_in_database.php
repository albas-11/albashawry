<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. تحديث جدول الإعدادات
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['telr_store_id', 'telr_auth_key', 'telr_mode']);
            $table->string('tap_public_key')->nullable()->after('seo_keywords');
            $table->string('tap_secret_key')->nullable()->after('tap_public_key');
        });

        // 2. تغيير اسم جدول الحركات وتحديث الأعمدة
        Schema::rename('telr_transactions', 'tap_transactions');
        Schema::table('tap_transactions', function (Blueprint $table) {
            $table->renameColumn('telr_order_ref', 'charge_id');
        });
    }

    public function down()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('telr_store_id')->nullable();
            $table->string('telr_auth_key')->nullable();
            $table->string('telr_mode')->nullable();
            $table->dropColumn(['tap_public_key', 'tap_secret_key']);
        });

        Schema::rename('tap_transactions', 'telr_transactions');
        Schema::table('telr_transactions', function (Blueprint $table) {
            $table->renameColumn('charge_id', 'telr_order_ref');
        });
    }
};