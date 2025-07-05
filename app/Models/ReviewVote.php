<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReviewVote extends Model
{
    // use HasFactory;

    protected $table = 'review_votes';
    protected $primaryKey = 'review_vote_id';
    protected $keyType = 'int';
    public $timestamps = true;
    public $incrementing = true;

    protected $fillable = [
        'review_id',
        'user_id',
        'vote_type',
    ];

    public function review(): BelongsTo
    {
        return $this->belongsTo(Review::class, 'review_id', 'review_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
