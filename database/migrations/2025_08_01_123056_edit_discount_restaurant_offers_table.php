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
        Schema::table('restaurant_offers', function (Blueprint $table) {
            $table->dropColumn('discount_percentage');
            $table->dropColumn('discount_amount');
            $table->string('discount_type')->nullable()->after('description');
            $table->string('discount')->nullable()->after('discount_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('restaurant_offers', function (Blueprint $table) {
            $table->string('discount_percentage')->nullable();
            $table->string('discount_amount')->nullable();
            $table->dropColumn('discount_type');
            $table->dropColumn('discount');
        });
    }
};
