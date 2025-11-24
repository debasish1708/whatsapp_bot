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
        Schema::create('school_category_school', function (Blueprint $table) {
            // $table->id();
            $table->foreignUuid('school_id')->constrained('schools');
            $table->foreignUuid('school_category_id')->constrained('school_categories');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_category_school');
    }
};
