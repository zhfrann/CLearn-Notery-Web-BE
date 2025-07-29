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
    public function getAllReports(Request $request) {}

    public function getAllNotesSubmission(Request $request) {}
}
