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
        Schema::create('school_psychological_support_office_hours', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('school_psychological_support_id')->constrained('school_psychological_supports');
            $table->string('day');
            $table->time('start_time');
            $table->time('end_time');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_psychological_support_office_hours');
    }
};
