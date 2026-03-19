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
        Schema::dropIfExists('tier_features');

        Schema::create('tier_features', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tier_id');
            $table->string('feature_description');
            $table->boolean('is_available')->default(true);
            $table->integer('display_order')->default(0);
            
            $table->foreign('tier_id')->references('id')->on('product_pricing_tiers')->onDelete('cascade');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tier_features');
    }
};
