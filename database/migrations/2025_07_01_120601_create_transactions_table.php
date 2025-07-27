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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id('transaction_id');
            $table->foreignId('note_id')->constrained('notes', 'note_id')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('buyer_id')->constrained('users', 'user_id')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('status');
            $table->datetime('tgl_transaksi')->useCurrent();
            $table->text('catatan')->nullable();
            $table->string('bukti_pembayaran')->nullable();
            $table->timestamps();

            $table->decimal('jumlah', 10, 2)->default(0); // Total amount yang dibayar buyer
            $table->string('midtrans_order_id')->nullable();
            $table->string('midtrans_transaction_id')->nullable();
            $table->string('snap_token')->nullable();
            $table->string('payment_method')->nullable();
            $table->decimal('platform_fee', 10, 2)->default(0); // Fee yang dipotong platform
            $table->decimal('seller_amount', 10, 2)->default(0); // Amount yang masuk ke seller
            // $table->string('disbursement_id')->nullable();
            // $table->enum('disbursement_status', ['pending', 'processing', 'success', 'failed'])->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
