<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PayoutMethod extends Model
{
    // use HasFactory;

    protected $table = 'payout_methods';
    protected $primaryKey = 'payout_method_id';
    protected $keyType = 'int';
    public $timestamps = true;
    public $incrementing = true;

    protected $fillable = [
        'user_id',
        'tipe_pembayaran',
        'nama_penerima',
        'akun_penerima',
        'kode_bank'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function withdrawRequests(): HasMany
    {
        return $this->hasMany(WithdrawRequest::class, 'payout_method_id', 'payout_method_id');
    }
}
