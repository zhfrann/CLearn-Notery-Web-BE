<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Semester extends Model
{
    // use HasFactory;

    protected $table = 'semesters';
    protected $primaryKey = 'semester_id';
    protected $keyType = 'int';
    public $timestamps = true;
    public $incrementing = true;

    protected $fillable = [
        'major_id',
        'nomor_semester',
        'tahun_ajaran',
        'periode'
    ];

    public function major(): BelongsTo
    {
        return $this->belongsTo(Major::class, 'major_id', 'major_id');
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class, 'semester_id', 'semester_id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'semester_id', 'semester_id');
    }
}
