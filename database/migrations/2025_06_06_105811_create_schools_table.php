<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('schools', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users');
            // $table->foreignUuid('category_id')->nullable()->constrained('school_categories');
            $table->string('logo')->nullable();
            $table->string('mobile_number')->nullable();
            $table->text('address')->nullable();
            $table->string('address_link')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->string('pincode')->nullable();
            $table->text('services')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP TABLE IF EXISTS schools CASCADE;');
    }
};
