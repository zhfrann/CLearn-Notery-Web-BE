<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Major extends Model
{
    // use HasFactory;

    protected $table = 'majors';
    protected $primaryKey = 'major_id';
    protected $keyType = 'int';
    public $timestamps = true;
    public $incrementing = true;

    protected $fillable = [
        'faculty_id',
        'nama_jurusan',
        'kode_jurusan'
    ];

    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class, 'faculty_id', 'faculty_id');
    }

    public function semesters(): HasMany
    {
        return $this->hasMany(Semester::class, 'major_id', 'major_id');
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class, 'major_id', 'major_id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'major_id', 'major_id');
    }
}
