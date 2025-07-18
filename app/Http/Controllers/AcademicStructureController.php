<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Faculty;
use App\Models\Major;
use App\Models\Semester;
use App\Models\User;
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

    public function updateAcademic(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'faculty_id' => 'nullable|exists:faculties,faculty_id',
            'major_id' => 'nullable|exists:majors,major_id',
            'semester_id' => 'nullable|exists:semesters,semester_id',
        ]);

        if ($request->has('faculty_id')) {
            $user->faculty_id = $request->faculty_id;
        }

        if ($request->has('major_id')) {
            $user->major_id = $request->major_id;
        }

        if ($request->has('semester_id')) {
            $user->semester_id = $request->semester_id;
        }

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Data akademik berhasil diperbarui',
            'data' => [
                'user_id' => $user->id,
                'username' => $user->username,
                'nama' => $user->nama,
                'faculty_id' => $user->faculty_id,
                'major_id' => $user->major_id,
                'semester_id' => $user->semester_id,
            ]
        ]);
    }

    public function addFavoriteCourse(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'course_id' => 'required|exists:courses,course_id'
        ]);

        // Cek apakah sudah ada di favorit
        $exists = $user->favoriteCourses()->where('course_id', $request->course_id)->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Mata kuliah sudah ada di favorit',
                'data' => null
            ], 400);
        }

        $favoriteCourse = $user->favoriteCourses()->create([
            'course_id' => $request->course_id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Berhasil menambah mata kuliah favorit',
            'data' => [
                'favorite_course_id' => $favoriteCourse->favorite_course_id,
                'course' => [
                    'course_id' => $favoriteCourse->course->course_id,
                    'nama_mk' => $favoriteCourse->course->nama_mk
                ]
            ]
        ]);
    }

    public function removeFavoriteCourse(Request $request, string $id)
    {
        $user = $request->user();

        $favoriteCourse = $user->favoriteCourses()->where('course_id', $id)->first();

        if (!$favoriteCourse) {
            return response()->json([
                'success' => false,
                'message' => 'Mata kuliah tidak ada di favorit',
                'data' => null
            ], 404);
        }

        $favoriteCourse->delete();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil menghapus mata kuliah favorit',
            'data' => null
        ]);
    }

    public function getFavoriteCourses(Request $request)
    {
        $user = $request->user();

        $favoriteCourses = $user->favoriteCourses()
            ->with('course.major.faculty', 'course.semester')
            ->get()
            ->map(function ($favorite) {
                return [
                    'favorite_course_id' => $favorite->favorite_course_id,
                    'course' => [
                        'course_id' => $favorite->course->course_id,
                        'nama_mk' => $favorite->course->nama_mk,
                        'major' => [
                            'major_id' => $favorite->course->major->major_id,
                            'nama_jurusan' => $favorite->course->major->nama_jurusan,
                            'faculty' => [
                                'faculty_id' => $favorite->course->major->faculty->faculty_id,
                                'nama_fakultas' => $favorite->course->major->faculty->nama_fakultas,
                            ]
                        ],
                        'semester' => [
                            'semester_id' => $favorite->course->semester->semester_id,
                            'nomor_semester' => $favorite->course->semester->nomor_semester,
                        ]
                    ]
                ];
            });

        return response()->json([
            'success' => true,
            'message' => 'Daftar mata kuliah favorit',
            'data' => $favoriteCourses
        ]);
    }
}
