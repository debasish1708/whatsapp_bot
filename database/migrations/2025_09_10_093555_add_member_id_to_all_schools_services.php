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
        Schema::table('school_sos_alerts', function (Blueprint $table) {
            $table->foreignUuid('member_id')->nullable()->constrained('users')->after('school_id');
        });
        // this is for both schools and restaurants
        Schema::table('annoucements', function (Blueprint $table) {
            $table->foreignUuid('member_id')->nullable()->constrained('users')->after('businessable_id');
        });
        Schema::table('school_events', function (Blueprint $table) {
            $table->foreignUuid('member_id')->nullable()->constrained('users')->after('school_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('school_sos_alerts', function (Blueprint $table) {
            $table->dropForeign(['member_id']);
            $table->dropColumn('member_id');
        });
        // this is for both schools and restaurants
        Schema::table('annoucements', function (Blueprint $table) {
            $table->dropForeign(['member_id']);
            $table->dropColumn('member_id');
        });
        Schema::table('school_events', function (Blueprint $table) {
            $table->dropForeign(['member_id']);
            $table->dropColumn('member_id');
        });
    }
};
