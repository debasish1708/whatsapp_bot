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
        Schema::create('restaurant_sustainability', function (Blueprint $table) {
            $table->foreignUuid('restaurant_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('sustainability_id')->constrained()->onDelete('cascade');
            $table->primary(['restaurant_id', 'sustainability_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restaurant_sustainability');
    }
};
