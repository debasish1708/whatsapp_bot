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
            $table->foreignUuid('member_id')->nullable()->constrained('users')->after('restaurant_id');
        });
        // Schema::table('annoucements', function (Blueprint $table) {
        //     $table->foreignUuid('member_id')->nullable()->constrained('users')->after('businessable_id');
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('restaurant_offers', function (Blueprint $table) {
            $table->dropForeign(['member_id']);
            $table->dropColumn('member_id');
        });
        // Schema::table('annoucements', function (Blueprint $table) {
        //     $table->dropForeign(['member_id']);
        //     $table->dropColumn('member_id');
        // });
    }
};
