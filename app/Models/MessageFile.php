<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MessageFile extends Model
{
    // use HasFactory;

    protected $table = 'message_files';
    protected $primaryKey = 'message_file_id';
    protected $keyType = 'int';
    public $timestamps = true;
    public $incrementing = true;

    protected $fillable = [
        'message_id',
        'nama_file',
        'path_file',
        'tipe',
    ];

    public function message(): BelongsTo
    {
        return $this->belongsTo(Message::class, 'message_id', 'message_id');
    }
}
