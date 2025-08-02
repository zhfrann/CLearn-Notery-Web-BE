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
        Schema::create('notification_files', function (Blueprint $table) {
            $table->id('notification_file_id');
            $table->foreignId('notification_id')->constrained('notifications', 'notification_id')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('nama_file')->nullable();
            $table->string('path_file');
            $table->string('tipe')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_files');
    }
};
