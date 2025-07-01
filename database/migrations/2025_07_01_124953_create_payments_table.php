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
        Schema::create('payments', function (Blueprint $table) {
            $table->id('payment_id');
            $table->foreignId('transaction_id')->constrained('transactions', 'transaction_id')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('status');
            $table->string('bukti_pembayaran')->nullable();
            $table->datetime('tgl_bayar')->nullable();
            $table->string('tipe_pembayaran');
            $table->string('kode_pembayaran');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
