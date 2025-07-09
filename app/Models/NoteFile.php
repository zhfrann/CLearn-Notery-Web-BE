<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NoteFile extends Model
{
    use HasFactory;

    protected $table = 'note_files';
    protected $primaryKey = 'note_file_id';
    protected $keyType = 'int';
    public $timestamps = true;
    public $incrementing = true;

    protected $fillable = [
        'note_id',
        'nama_file',
        'path_file',
        'tipe'
    ];

    public function note(): BelongsTo
    {
        return $this->belongsTo(Note::class, 'note_id', 'note_id');
    }
}
