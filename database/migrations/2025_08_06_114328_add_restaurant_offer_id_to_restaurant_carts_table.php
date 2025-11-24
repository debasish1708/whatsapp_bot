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
        Schema::table('restaurant_carts', function (Blueprint $table) {
          $table->foreignUuid('restaurant_offer_id')
                ->nullable()
                ->constrained('restaurant_offers');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('restaurant_carts', function (Blueprint $table) {
            //
        });
    }
};
