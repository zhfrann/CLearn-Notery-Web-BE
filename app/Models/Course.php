<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    // use HasFactory;
    protected $table = 'courses';
    protected $primaryKey = 'course_id';
    protected $keyType = 'int';
    public $timestamps = true;
    public $incrementing = true;

    protected $fillable = [
        'semester_id',
        'major_id',
        'nama_mk'
    ];

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class, 'semester_id', 'semester_id');
    }

    public function major(): BelongsTo
    {
        return $this->belongsTo(Major::class, 'major_id', 'major_id');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(Note::class, 'course_id', 'course_id');
    }

    public function favoriteByUsers(): HasMany
    {
        return $this->hasMany(User::class, 'matkul_favorit', 'course_id');
    }
}
