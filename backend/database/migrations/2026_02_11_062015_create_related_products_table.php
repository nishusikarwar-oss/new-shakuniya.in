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
        Schema::dropIfExists('related_products');

        Schema::create('related_products', function (Blueprint $table) {
            $table->uuid('product_id');
            $table->uuid('related_product_id');
            $table->string('relationship_type')->default('cross-sell'); // upsell, cross-sell, alternative
            $table->integer('display_order')->default(0);
            $table->timestamp('created_at')->useCurrent();
            
            $table->primary(['product_id', 'related_product_id']);
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('related_product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('related_products');
    }
};
