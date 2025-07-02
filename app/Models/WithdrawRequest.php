<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WithdrawRequest extends Model
{
    // use HasFactory;

    protected $table = 'withdraw_requests';
    protected $primaryKey = 'withdraw_request_id';
    protected $keyType = 'int';
    public $timestamps = true;
    public $incrementing = true;

    protected $fillable = [
        'user_id',
        'payout_method_id',
        'jumlah',
        'status',
        'tgl_request',
        'tgl_transfer'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function payoutMethod(): BelongsTo
    {
        return $this->belongsTo(PayoutMethod::class, 'payout_method_id', 'payout_method_id');
    }
}
