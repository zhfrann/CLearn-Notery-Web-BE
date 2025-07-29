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
        Schema::create('reports', function (Blueprint $table) {
            $table->id('report_id');
            $table->foreignId('reporter_id')->constrained('users', 'user_id')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('reported_user_id')->constrained('users', 'user_id')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('report_type_value'); // Reference to report_types.value
            $table->foreign('report_type_value')->references('value')->on('report_types')->cascadeOnUpdate()->restrictOnDelete();
            $table->text('description'); // Keterangan lebih lanjut
            $table->json('evidence_files')->nullable(); // File bukti yang diupload
            $table->enum('status', ['pending', 'investigating', 'resolved', 'rejected'])->default('pending');
            $table->text('admin_notes')->nullable(); // Catatan dari admin
            $table->foreignId('handled_by_admin_id')->nullable()->constrained('users', 'user_id');
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            // Prevent duplicate reports for same issue
            $table->unique(['reporter_id', 'reported_user_id', 'report_type_value']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
