<?php

namespace App\Rules;

use App\Models\ReportType;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidReportType implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!ReportType::isValidType($value)) {
            $fail('Jenis laporan yang dipilih tidak valid atau tidak aktif.');
        }
    }
}
