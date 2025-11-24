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
        Schema::create('school_admission_payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('school_admission_id')->constrained('school_admissions');
            $table->string('amount')->nullable();
            $table->string('currency')->nullable();
            $table->string('status')->default('pending');
            $table->string('payment_method')->nullable();
            $table->string('transaction_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_admission_payments');
    }
};
