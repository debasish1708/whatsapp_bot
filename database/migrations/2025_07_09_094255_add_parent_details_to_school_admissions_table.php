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
        Schema::table('school_admissions', function (Blueprint $table) {
            $table->string('parents_name')->nullable();
            $table->string('parent_mobile_number')->nullable();
            $table->string('grade')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('school_admissions', function (Blueprint $table) {
            $table->dropColumn('parent_name');
            $table->dropColumn('parent_mobile_number');
            $table->dropColumn('grade');
        });
    }
};
