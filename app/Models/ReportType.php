<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportType extends Model
{
    use HasFactory;

    protected $primaryKey = 'report_type_id';

    protected $fillable = [
        'value',
        'label',
        'description',
        'is_active',
        'sort_order'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer'
    ];

    /**
     * Scope untuk hanya mengambil report types yang aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope untuk ordering berdasarkan sort_order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('label');
    }

    /**
     * Relationship dengan reports
     */
    public function reports()
    {
        return $this->hasMany(Report::class, 'report_type_value', 'value');
    }

    /**
     * Get formatted data for API response
     */
    public function toApiArray()
    {
        return [
            'value' => $this->value,
            'label' => $this->label,
            'description' => $this->description
        ];
    }

    /**
     * Static method untuk get semua active report types
     */
    public static function getActiveTypes()
    {
        return self::active()->ordered()->get();
    }

    /**
     * Static method untuk validate report type value
     */
    public static function isValidType($value)
    {
        return self::active()->where('value', $value)->exists();
    }
}
