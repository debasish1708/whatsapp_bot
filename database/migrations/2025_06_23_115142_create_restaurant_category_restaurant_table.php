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
        Schema::create('restaurant_category_restaurant', function (Blueprint $table) {
            // $table->id();
            $table->foreignUuid('restaurant_id')->constrained('restaurants');
            $table->foreignUuid('restaurant_category_id')->constrained('restaurant_categories');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restaurant_category_restaurant');
    }
};
