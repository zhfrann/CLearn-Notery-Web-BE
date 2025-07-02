<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    // use HasFactory;

    protected $table = 'payments';
    protected $primaryKey = 'payment_id';
    protected $keyType = 'int';
    public $timestamps = true;
    public $incrementing = true;

    protected $fillable = [
        'transaction_id',
        'status',
        'bukti_pembayaran',
        'tgl_bayar',
        'tipe_pembayaran',
        'kode_pembayaran'
    ];

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'transaction_id', 'transaction_id');
    }
}
