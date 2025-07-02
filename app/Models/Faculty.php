<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Faculty extends Model
{
    // use HasFactory;

    protected $table = 'faculties';
    protected $primaryKey = 'faculty_id';
    protected $keyType = 'int';
    public $timestamps = true;
    public $incrementing = true;

    protected $fillable = [
        'nama_fakultas',
        'kode_fakultas'
    ];

    public function majors(): HasMany
    {
        return $this->hasMany(Major::class, 'faculty_id', 'faculty_id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'faculty_id', 'faculty_id');
    }
}
