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
        Schema::dropIfExists('product_pricing_tiers');

        Schema::create('product_pricing_tiers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('product_id');
            $table->string('tier_name');
            $table->decimal('price_usd', 12, 2)->default(0);
            $table->decimal('price_inr', 12, 2)->default(0);
            $table->string('billing_period')->default('monthly'); // monthly, yearly, one-time
            $table->boolean('is_popular')->default(false);
            $table->integer('display_order')->default(0);
            $table->boolean('is_active')->default(true);
            
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_pricing_tiers');
    }
};
