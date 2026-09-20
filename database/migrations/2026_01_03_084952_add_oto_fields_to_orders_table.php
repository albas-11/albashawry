<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('oto_order_id')->unique()->nullable()->after('id');
            $table->decimal('weight', 8, 2)->nullable()->after('total');
            $table->decimal('height', 8, 2)->nullable()->after('weight');
            $table->decimal('width', 8, 2)->nullable()->after('height');
            $table->decimal('length', 8, 2)->nullable()->after('width');
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['oto_order_id', 'weight', 'height', 'width', 'length']);
        });
    }
};
