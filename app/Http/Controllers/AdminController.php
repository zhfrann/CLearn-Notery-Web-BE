<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\User;
use App\Models\UserAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    // GET /api/admin/reports - Get all reports for admin
    public function getAllReports(Request $request)
    {
        // Validasi query parameters untuk pagination
        $validated = $request->validate([
            'size' => 'nullable|integer|min:1|max:100',
            'page' => 'nullable|integer|min:1',
        ]);

        $size = $validated['size'] ?? 15;
        $page = $validated['page'] ?? 1;

        // Group laporan berdasarkan reported_user_id dan report_type_value
        $query = Report::with(['reportedUser', 'reportType'])
            ->select('reported_user_id', 'report_type_value', DB::raw('COUNT(*) as jumlah_laporan'), 'created_at')
            ->groupBy('reported_user_id', 'report_type_value', 'created_at')
            ->orderByDesc('jumlah_laporan');

        $paginated = $query->paginate($size, ['*'], 'page', $page);

        $result = $paginated->getCollection()->map(function ($row) {
            $reportedUser = $row->reportedUser;
            $reportType = $row->reportType;
            $title = ($reportType ? $reportType->label : $row->report_type_value) . ': Terlapor dengan ' . ($reportedUser ? $reportedUser->username : '-');
            return [
                'title' => $title,
                'jumlah_laporan' => $row->jumlah_laporan,
                'created_at' => $row->created_at->toIso8601String()
            ];
        });

        // Metadata pagination (opsional)
        $paginationMeta = [
            'current_page' => $paginated->currentPage(),
            'per_page' => $paginated->perPage(),
            'total' => $paginated->total(),
            'last_page' => $paginated->lastPage(),
            'from' => $paginated->firstItem(),
            'to' => $paginated->lastItem(),
            'has_more_pages' => $paginated->hasMorePages(),
            'path' => $paginated->path(),
            'links' => [
                'first' => $paginated->url(1),
                'last' => $paginated->url($paginated->lastPage()),
                'prev' => $paginated->previousPageUrl(),
                'next' => $paginated->nextPageUrl(),
            ]
        ];

        return response()->json([
            'success' => true,
            'message' => 'Daftar semua laporan',
            'data' => $result,
            'pagination' => $paginationMeta,
        ]);
    }

    public function getAllNotesSubmission(Request $request) {}
}
