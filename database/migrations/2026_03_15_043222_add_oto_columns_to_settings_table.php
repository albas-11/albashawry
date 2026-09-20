<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('settings', function (Blueprint $table) {
            // إضافة حقول OTO
            if (!Schema::hasColumn('settings', 'oto_client_id')) {
                $table->string('oto_client_id')->nullable();
                $table->string('oto_client_secret')->nullable();
                $table->string('oto_mode')->default('test')->nullable();
                $table->string('oto_warehouse_code')->nullable();
                $table->string('oto_warehouse_name')->nullable();
                $table->string('oto_warehouse_phone')->nullable();
                $table->string('oto_warehouse_city')->nullable();
                $table->string('oto_warehouse_address')->nullable();
                $table->string('oto_webhook_secret')->nullable();
            }

            // إضافة حقول Tap Payments كإجراء احترازي إذا لم تكن موجودة
            if (!Schema::hasColumn('settings', 'tap_public_key')) {
                $table->string('tap_public_key')->nullable();
                $table->string('tap_secret_key')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'oto_client_id',
                'oto_client_secret',
                'oto_mode',
                'oto_warehouse_code',
                'oto_warehouse_name',
                'oto_warehouse_phone',
                'oto_warehouse_city',
                'oto_warehouse_address',
                'oto_webhook_secret',
                'tap_public_key',
                'tap_secret_key'
            ]);
        });
    }
};