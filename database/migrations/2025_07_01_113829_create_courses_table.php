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
        Schema::create('courses', function (Blueprint $table) {
            $table->id('course_id');
            $table->foreignId('semester_id')->constrained('semesters', 'semester_id')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('major_id')->constrained('majors', 'major_id')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('nama_mk');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
