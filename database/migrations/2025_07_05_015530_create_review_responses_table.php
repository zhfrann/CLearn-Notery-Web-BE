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
        Schema::create('review_responses', function (Blueprint $table) {
            $table->id('review_response_id');
            $table->foreignId('review_id')->constrained('reviews', 'review_id')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('seller_id')->constrained('users', 'user_id')->cascadeOnUpdate()->cascadeOnDelete();
            $table->text('respon');
            $table->dateTime('tgl_respon')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('review_responses');
    }
};
