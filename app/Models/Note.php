<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Note extends Model
{
    // use HasFactory;

    protected $table = 'notes';
    protected $primaryKey = 'note_id';
    protected $keyType = 'int';
    public $timestamps = true;
    public $incrementing = true;

    protected $fillable = [
        'seller_id',
        'course_id',
        'judul',
        'deskripsi',
        'harga',
        'nama_file',
        'jumlah_terjual',
        'jumlah_like',
        'jumlah_dikunjungi',
        'gambar_preview'
    ];

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id', 'user_id');
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id', 'course_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'note_id', 'note_id');
    }

    public function savedByUsers(): HasMany
    {
        return $this->hasMany(SavedNote::class, 'note_id', 'note_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'note_id', 'note_id');
    }

    public function noteTags(): HasMany
    {
        return $this->hasMany(NoteTag::class, 'note_id', 'note_id');
    }

    public function files(): HasMany
    {
        return $this->hasMany(NoteFile::class, 'note_id', 'note_id');
    }

    public function noteStatus(): HasOne
    {
        return $this->hasOne(NoteStatus::class, 'note_id', 'note_id');
    }

    public function scopeApproved($query)
    {
        return $query->whereHas('noteStatus', function ($q) {
            $q->where('status', 'diterima');
        });
    }
}
