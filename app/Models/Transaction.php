<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Transaction extends Model
{
    // use HasFactory;

    protected $table = 'transactions';
    protected $primaryKey = 'transaction_id';
    protected $keyType = 'int';
    public $timestamps = true;
    public $incrementing = true;

    protected $fillable = [
        'note_id',
        'buyer_id',
        'status',
        'tgl_transaksi',
        'catatan',
        'bukti_pembayaran',
        'jumlah',
        'midtrans_order_id',
        'midtrans_transaction_id',
        'snap_token',
        'payment_method',
        'platform_fee',
        'seller_amount'
    ];

    public function note(): BelongsTo
    {
        return $this->belongsTo(Note::class, 'note_id', 'note_id');
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id', 'user_id');
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class, 'transaction_id', 'transaction_id');
    }
}
