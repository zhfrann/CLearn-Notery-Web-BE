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
        Schema::create('notes', function (Blueprint $table) {
            $table->id('note_id');
            $table->foreignId('seller_id')->constrained('users', 'user_id')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained('courses', 'course_id')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('judul');
            $table->text('deskripsi');
            $table->integer('harga')->default(0);
            // $table->string('nama_file');
            // $table->unsignedInteger('jumlah_terjual')->default(0);
            // $table->string('status')->nullable();
            $table->unsignedInteger('jumlah_like')->default(0);
            $table->unsignedInteger('jumlah_dikunjungi')->default(0);
            $table->string('gambar_preview')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notes');
    }
};
