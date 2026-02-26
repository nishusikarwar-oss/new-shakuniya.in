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
        Schema::create('job_category_mapping', function (Blueprint $table) {
            $table->foreignId('job_id')
                  ->constrained('job_openings')
                  ->onDelete('cascade');
                  
            $table->foreignId('category_id')
                  ->constrained('job_categories')
                  ->onDelete('cascade');
                  
            $table->primary(['job_id', 'category_id']);
            
            // Indexes for better performance
            $table->index('job_id');
            $table->index('category_id');
            
            // Optional: add timestamps if you want to track when mapping was created
            // $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_category_mapping');
    }
};