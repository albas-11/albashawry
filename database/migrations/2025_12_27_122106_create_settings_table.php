<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;


return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();

            // Informations Générales
            $table->string('site_name')->default('Mon Site');
            $table->string('locale')->default('fr');
            $table->string('logo')->nullable();
            $table->string('favicon')->nullable();

            // Coordonnées
            $table->string('admin_email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();

            // Configuration SMTP
            $table->string('smtp_host')->nullable();
            $table->integer('smtp_port')->default(587);
            $table->string('smtp_encryption')->default('tls');
            $table->string('smtp_username')->nullable();
            $table->text('smtp_password')->nullable(); // Encrypted

            // SEO
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->text('seo_keywords')->nullable();

            // Paiement Telr
            $table->string('telr_store_id')->nullable();
            $table->text('telr_auth_key')->nullable(); // Encrypted
            $table->enum('telr_mode', ['test', 'live'])->default('test');
            $table->string('currency')->default('MAD');

            // Livraison Aramex
            $table->string('aramex_username')->nullable();
            $table->text('aramex_password')->nullable(); // Encrypted
            $table->string('aramex_account_number')->nullable();
            $table->string('aramex_account_pin')->nullable();
            $table->string('aramex_entity')->default('AMM');
            $table->enum('aramex_mode', ['test', 'live'])->default('test');
            $table->decimal('shipping_cost', 8, 2)->default(0);
            $table->decimal('free_shipping_threshold', 8, 2)->default(0);

            $table->timestamps();
        });

        // Insert default settings
        DB::table('settings')->insert([
            'site_name' => config('app.name'),
            'locale' => 'fr',
            'currency' => 'MAD',
            'smtp_port' => 587,
            'smtp_encryption' => 'tls',
            'telr_mode' => 'test',
            'aramex_mode' => 'test',
            'aramex_entity' => 'AMM',
            'shipping_cost' => 0,
            'free_shipping_threshold' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
