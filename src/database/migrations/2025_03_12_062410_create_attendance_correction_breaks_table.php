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
        Schema::create('attendance_correction_breaks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_correction_id')->constrained()->onDelete('cascade');
            $table->foreignId('break_id')->constrained('breaks')->onDelete('cascade');
            $table->dateTime('original_break_start')->nullable();
            $table->dateTime('original_break_end')->nullable();
            $table->dateTime('corrected_break_start')->nullable();
            $table->dateTime('corrected_break_end')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_correction_breaks');
    }
};
