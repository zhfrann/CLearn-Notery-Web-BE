<?php

use App\Http\Controllers\AcademicStructureController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProfileDataController;
use App\Http\Controllers\ReviewController;
use App\Http\Resources\UserResource;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return response()->json([
        'success' => true,
        'message' => 'Data informasi user.',
        'data' => new UserResource($request->user()->load(['semester', 'major', 'faculty']))
    ]);
});

Route::prefix('/auth')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
    });

    Route::delete('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
});

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/profile', [ProfileController::class, 'getProfileDetail']);
    Route::put('/profile', [ProfileController::class, 'updateProfile']);
    // Route::put('/profile/password', [ProfileController::class, 'changePassword']);
    // Route::put('/profile/photo', [ProfileController::class, 'updatePhoto']);
    Route::get('/profile/notes', [ProfileController::class, 'getNotes']);

    Route::get('/profile/product-status', [ProfileDataController::class, 'productStatus']);
    Route::get('/profile/transactions', [ProfileDataController::class, 'transactions']);
    // Route::get('/profile/favorites-notes', [ProfileDataController::class, 'favoritesNotes']);

    Route::get('/faculties', [AcademicStructureController::class, 'getFaculties']);
    Route::get('/majors', [AcademicStructureController::class, 'getMajors']);
    Route::get('/semesters', [AcademicStructureController::class, 'getSemesters']);
    Route::get('/courses', [AcademicStructureController::class, 'getCourses']);

    Route::get('/notes/latest-notes', [NoteController::class, 'latestNotes']);
    Route::get('/notes/most-liked-notes', [NoteController::class, 'mostLikeNotes']);
    Route::post('/top-creator', [NoteController::class, 'topCreator']);

    Route::get('/notes', [NoteController::class, 'getAllNotes']);
    Route::post('/notes', [NoteController::class, 'createNote']);

    Route::get('/notes/{id}', [NoteController::class, 'getNoteDetail']);
    Route::post('/notes/{id}/like', [NoteController::class, 'likeNote']);
    Route::delete('/notes/{id}/like', [NoteController::class, 'unlikeNote']);
    Route::post('/notes/{id}/favorite', [NoteController::class, 'addFavoriteNote']);
    Route::delete('/notes/{id}/favorite', [NoteController::class, 'removeFavoriteNote']);

    Route::put('/notes/{id}', [NoteController::class, 'updateNote']);
    // Route::delete('/notes/{id}', [NoteController::class, 'deleteNote']);
    // Route::get('/notes/{id}/buy', [NoteController::class, 'buyNote']);

    Route::get('/notes/{id}/reviews', [ReviewController::class, 'getReviews']);
    Route::post('/notes/{id}/reviews', [ReviewController::class, 'createReview']);
    // Route::put('/notes/{id}/reviews', [ReviewController::class, 'updateReview']);
    // Route::delete('/notes/{id}/reviews', [ReviewController::class, 'deleteReview']);


    // Route::prefix('/user')->group(function () {
    //     // Route::get(/{id}/notes, [...]);
    //     // Route::get(/{id}/reviews, [...]);
    // });

    // ...
});
