<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Review extends Model
{
    // use HasFactory;

    protected $table = 'reviews';
    protected $primaryKey = 'review_id';
    protected $keyType = 'int';
    public $timestamps = true;
    public $incrementing = true;

    protected $fillable = [
        'user_id',
        'note_id',
        'komentar',
        'rating',
        'tgl_review'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function note(): BelongsTo
    {
        return $this->belongsTo(Note::class, 'note_id', 'note_id');
    }

    public function response(): HasOne
    {
        return $this->hasOne(ReviewResponse::class, 'review_id', 'review_id');
    }

    public function votes(): HasMany
    {
        return $this->hasMany(ReviewVote::class, 'review_id', 'review_id');
    }

    public function getLikeCountAttribute()
    {
        return $this->votes()->where('tipe_vote', '=', 'like')->count();
    }

    public function getDislikeCountAttribute()
    {
        return $this->votes()->where('tipe_vote', '=', 'dislike')->count();
    }
}
