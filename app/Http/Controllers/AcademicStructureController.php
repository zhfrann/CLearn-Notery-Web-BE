<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Faculty;
use App\Models\Major;
use App\Models\Semester;
use Illuminate\Http\Request;

class AcademicStructureController extends Controller
{
    public function getFaculties()
    {
        $faculties = Faculty::select('faculty_id', 'nama_fakultas', 'kode_fakultas')->get();

        return response()->json([
            'success' => true,
            'message' => '',
            'data' => $faculties
        ]);
    }

    public function getMajors(Request $request)
    {
        $request->validate([
            'faculty_id' => 'required|exists:faculties,faculty_id'
        ]);

        $majors = Major::where('faculty_id', $request->faculty_id)
            ->select('major_id', 'nama_jurusan', 'kode_jurusan')
            ->get();

        return response()->json([
            'success' => true,
            'message' => '',
            'data' => $majors
        ]);
    }

    public function getSemesters(Request $request)
    {
        $request->validate([
            'major_id' => 'required|exists:majors,major_id'
        ]);

        $semesters = Semester::where('major_id', $request->major_id)
            ->select('semester_id', 'nomor_semester', 'tahun_ajaran')
            ->get();

        return response()->json([
            'success' => true,
            'message' => '',
            'data' => $semesters
        ]);
    }

    public function getCourses(Request $request)
    {
        $request->validate([
            'major_id' => 'required|exists:majors,major_id',
            'semester_id' => 'required|exists:semesters,semester_id'
        ]);

        $courses = Course::where('major_id', $request->major_id)
            ->where('semester_id', $request->semester_id)
            ->select('course_id', 'nama_mk')
            ->get();

        return response()->json([
            'success' => true,
            'message' => '',
            'data' => $courses
        ]);
    }
}
