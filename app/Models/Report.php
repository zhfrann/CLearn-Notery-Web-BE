<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Report extends Model
{
    protected $table = 'reports';
    protected $primaryKey = 'report_id';

    protected $fillable = [
        'reporter_id',
        'reported_user_id',
        'report_type_value',
        'description',
        'evidence_files',
        'status',
        'admin_notes',
        'handled_by_admin_id',
        'resolved_at'
    ];

    protected $casts = [
        'evidence_files' => 'array',
        'resolved_at' => 'datetime'
    ];

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_id', 'user_id');
    }

    public function reportedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_user_id', 'user_id');
    }

    public function handledByAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by_admin_id', 'user_id');
    }

    public function reportType(): BelongsTo
    {
        return $this->belongsTo(ReportType::class, 'report_type_value', 'value');
    }

    // Scope untuk filter status
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    // Helper untuk get report type label
    public function getReportTypeLabel()
    {
        return $this->reportType ? $this->reportType->label : $this->report_type_value;
    }

    // Helper untuk get report type description
    public function getReportTypeDescription()
    {
        return $this->reportType ? $this->reportType->description : null;
    }
}
