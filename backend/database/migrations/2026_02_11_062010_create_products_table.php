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
        // We drop and recreate to ensure a clean structure matching the requirements
        Schema::dropIfExists('products');
        
        Schema::create('products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title', 200);
            $table->string('slug', 100)->unique();
            $table->text('short_description')->nullable();
            $table->longText('full_description')->nullable();
            $table->string('tags')->nullable();
            $table->decimal('price_usd', 12, 2)->default(0);
            $table->decimal('price_inr', 12, 2)->default(0);
            
            // Media
            $table->string('image')->nullable(); // Primary image path/URL
            $table->string('video_url')->nullable();
            $table->text('video_text')->nullable();
            
            // SEO Metadata
            $table->string('meta_title', 200)->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();
            $table->string('canonical_url')->nullable();
            
            // Social Media Metadata
            $table->string('og_title')->nullable();
            $table->text('og_description')->nullable();
            $table->string('og_image')->nullable();
            $table->string('twitter_title')->nullable();
            $table->text('twitter_description')->nullable();
            $table->string('twitter_image')->nullable();
            
            // Schema
            $table->longText('schema_markup')->nullable();
            
            // Settings
            $table->boolean('is_active')->default(true);
            $table->integer('display_order')->default(0);
            
            // Audit
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
