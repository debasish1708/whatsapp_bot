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
        Schema::create('offer_menu_item', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('offer_id')->constrained('restaurant_offers')->cascadeOnDelete();
            $table->foreignUuid('menu_item_id')->constrained('restaurant_menu_items')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offer_menu_item');
    }
};
