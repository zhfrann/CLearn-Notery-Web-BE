<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\ReportType;
use App\Models\User;
use App\Rules\ValidReportType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ReportController extends Controller
{
    // GET /api/reports/types - Get available report types
    public function getReportTypes()
    {
        $reportTypes = ReportType::getActiveTypes();

        return response()->json([
            'success' => true,
            'message' => 'Jenis laporan tersedia',
            'data' => $reportTypes->map(function ($type) {
                return $type->toApiArray();
            })
        ]);
    }

    // POST /api/reports - Submit new report
    public function submitReport(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'reported_user_id' => [
                'required',
                'exists:users,user_id',
                'different:' . $user->user_id, // Can't report yourself
            ],
            'report_type' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    if (!ReportType::isValidType($value)) {
                        $fail('Jenis laporan yang dipilih tidak valid.');
                    }
                }
            ],
            'description' => 'required|string|min:10|max:1000',
            'evidence_files' => 'nullable|array|max:10',
            'evidence_files.*' => 'file|mimes:jpg,jpeg,png,pdf,doc,docx|max:10240'
        ]);

        // Check if user already reported this user for same issue
        $existingReport = Report::where('reporter_id', $user->user_id)
            ->where('reported_user_id', $validated['reported_user_id'])
            ->where('report_type_value', $validated['report_type'])
            ->first();

        if ($existingReport) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah pernah melaporkan user ini untuk jenis pelanggaran yang sama'
            ], 400);
        }

        // Handle file uploads
        $evidenceFiles = [];
        if ($request->hasFile('evidence_files')) {
            foreach ($request->file('evidence_files') as $file) {
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('reports/evidence', $filename, 'public');

                $evidenceFiles[] = [
                    'original_name' => $file->getClientOriginalName(),
                    'stored_name' => $filename,
                    'path' => $path,
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType()
                ];
            }
        }

        // Create report
        $report = Report::create([
            'reporter_id' => $user->user_id,
            'reported_user_id' => $validated['reported_user_id'],
            'report_type_value' => $validated['report_type'],
            'description' => $validated['description'],
            'evidence_files' => $evidenceFiles,
            'status' => 'pending'
        ]);

        $reportedUser = User::find($validated['reported_user_id']);

        return response()->json([
            'success' => true,
            'message' => 'Laporan berhasil dikirim dan akan diproses oleh admin',
            'data' => [
                'report_id' => $report->report_id,
                'reporter' => [
                    "user_id" => $user->user_id,
                    "username" => $user->username,
                ],
                'reported_user' => [
                    "user_id" => $reportedUser->user_id,
                    "username" => $reportedUser->username
                ],
                'status' => $report->status,
                'report_type' => $report->getReportTypeLabel(),
                'created_at' => $report->created_at->toIso8601String()
            ]
        ]);
    }
}
