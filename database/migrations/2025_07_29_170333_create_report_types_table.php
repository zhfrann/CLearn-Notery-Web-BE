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
        Schema::create('report_types', function (Blueprint $table) {
            $table->id('report_type_id');
            $table->string('value')->unique(); // penyalahgunaan, penipuan_konten_tidak_sesuai, etc
            $table->string('label'); // Penyalahgunaan, Penipuan/konten tidak sesuai, etc
            $table->text('description')->nullable(); // Optional description for admin
            $table->boolean('is_active')->default(true); // Can be disabled by admin
            $table->integer('sort_order')->default(0); // For ordering in UI
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_types');
    }
};
