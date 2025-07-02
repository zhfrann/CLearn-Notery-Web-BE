<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tag extends Model
{
    // use HasFactory;

    protected $table = 'tags';
    protected $primaryKey = 'tag_id';
    protected $keyType = 'int';
    public $timestamps = true;
    public $incrementing = true;

    protected $fillable = [
        'nama_tag'
    ];

    public function noteTags(): HasMany
    {
        return $this->hasMany(NoteTag::class, 'tag_id', 'tag_id');
    }
}
