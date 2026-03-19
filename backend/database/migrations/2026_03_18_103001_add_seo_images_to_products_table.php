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
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'og_image')) {
                $table->string('og_image')->nullable()->after('og_description');
            }
            if (!Schema::hasColumn('products', 'twitter_image')) {
                $table->string('twitter_image')->nullable()->after('twitter_description');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['og_image', 'twitter_image']);
        });
    }
};
