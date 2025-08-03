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
        Schema::create('message_files', function (Blueprint $table) {
            $table->id('message_file_id');
            $table->foreignId('message_id')->constrained('messages', 'message_id')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('nama_file')->nullable();
            $table->string('path_file');
            $table->string('tipe')->nullable(); // pdf, docx, zip, dll
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('message_files');
    }
};
