<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NoteLike extends Model
{
    // use HasFactory;

    protected $table = 'note_likes';
    protected $primaryKey = 'note_like_id';
    protected $keyType = 'int';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'note_id',
        'user_id',
    ];

    // Relasi ke Note
    public function note(): BelongsTo
    {
        return $this->belongsTo(Note::class, 'note_id', 'note_id');
    }

    // Relasi ke User
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
