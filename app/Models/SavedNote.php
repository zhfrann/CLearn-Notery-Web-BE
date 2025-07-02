<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SavedNote extends Model
{
    // use HasFactory;

    protected $table = 'saved_notes';
    protected $primaryKey = 'saved_note_id';
    protected $keyType = 'int';
    public $timestamps = true;
    public $incrementing = true;

    protected $fillable = [
        'note_id',
        'user_id'
    ];

    public function note(): BelongsTo
    {
        return $this->belongsTo(Note::class, 'note_id', 'note_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
