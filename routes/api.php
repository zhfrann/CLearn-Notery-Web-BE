<?php

use App\Http\Controllers\AcademicStructureController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProfileDataController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\TagController;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return response()->json([
//         'success' => true,
//         'message' => 'Data informasi user.',
//         'data' => new UserResource($request->user()->load(['semester', 'major', 'faculty']))
//     ]);
// });

Route::prefix('/auth')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
    });

    Route::delete('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
});

Route::middleware(['auth:sanctum', 'checkUserActive'])->group(function () {

    Route::get('/profile', [ProfileController::class, 'getProfileDetail']);
    Route::put('/profile', [ProfileController::class, 'updateProfile']);
    // Route::put('/profile/password', [ProfileController::class, 'changePassword']);
    // Route::put('/profile/photo', [ProfileController::class, 'updatePhoto']);
    Route::get('/profile/notes', [ProfileController::class, 'getNotes']);
    Route::get('/profile/qr-code', [ProfileController::class, 'getQrCode']);
    Route::post('/profile/qr-code', [ProfileController::class, 'uploadQrCode']);

    Route::put('/profile/academic', [AcademicStructureController::class, 'updateAcademic']);
    Route::get('/profile/product-status', [ProfileDataController::class, 'productStatus']);
    Route::get('/profile/product-status/{id}', [ProfileDataController::class, 'productStatusDetail']);
    Route::get('/profile/transactions', [ProfileDataController::class, 'transactions']);
    Route::get('/profile/transactions/{id}', [ProfileDataController::class, 'transactionDetail']);
    // Route::get('/profile/favorites-notes', [ProfileDataController::class, 'favoritesNotes']);

    Route::get('/faculties', [AcademicStructureController::class, 'getFaculties']);
    Route::get('/majors', [AcademicStructureController::class, 'getMajors']);
    Route::get('/semesters', [AcademicStructureController::class, 'getSemesters']);
    Route::get('/courses', [AcademicStructureController::class, 'getCourses']);

    Route::get('/favorite-courses', [AcademicStructureController::class, 'getFavoriteCourses']);
    Route::post('/favorite-courses', [AcademicStructureController::class, 'addFavoriteCourse']);
    Route::delete('/favorite-courses/{id}', [AcademicStructureController::class, 'removeFavoriteCourse']);

    Route::get('/notes/latest-notes', [NoteController::class, 'latestNotes']);
    Route::get('/notes/most-liked-notes', [NoteController::class, 'mostLikeNotes']);
    Route::get('/top-creators', [NoteController::class, 'topCreator']);

    Route::get('/notes', [NoteController::class, 'getAllNotes']);
    Route::post('/notes', [NoteController::class, 'createNote']);

    Route::get('/notes/{id}', [NoteController::class, 'getNoteDetail']);
    Route::post('/notes/{id}/like', [NoteController::class, 'likeNote']);
    Route::delete('/notes/{id}/like', [NoteController::class, 'unlikeNote']);
    Route::post('/notes/{id}/favorite', [NoteController::class, 'addFavoriteNote']);
    Route::delete('/notes/{id}/favorite', [NoteController::class, 'removeFavoriteNote']);

    Route::post('/notes/{id}', [NoteController::class, 'updateNote']);
    Route::get('/notes/{id}/files', [NoteController::class, 'getFiles']);
    Route::delete('/notes/{id}', [NoteController::class, 'deleteNote']);
    // Route::post('/notes/{id}/buy', [NoteController::class, 'buyNote']);
    Route::post('/notes/{id}/buy', [NoteController::class, 'buyNoteMidtrans']);
    Route::post('/payment/manual-update', [NoteController::class, 'manualUpdatePayment']);

    Route::get('/notes/{id}/reviews', [ReviewController::class, 'getReviews']);
    Route::post('/notes/{id}/reviews', [ReviewController::class, 'createReview']);
    // Route::put('/notes/{id}/reviews', [ReviewController::class, 'updateReview']);
    // Route::delete('/notes/{id}/reviews', [ReviewController::class, 'deleteReview']);

    Route::post('/reviews/{id}/response', [ReviewController::class, 'createSellerResponse']);
    Route::post('/reviews/{id}/vote', [ReviewController::class, 'voteReview']);
    Route::delete('/reviews/{id}/vote', [ReviewController::class, 'unvoteReview']);

    Route::get('/tags', [TagController::class, 'getTags']);
    Route::get('/tags/popular', [TagController::class, 'getMostSearchTags']);
    Route::get('/tags/{id}', [TagController::class, 'getTagsDetail']);

    Route::post('/reports', [ReportController::class, 'submitReport']);
    Route::get('/reports/types', [ReportController::class, 'getReportTypes']);

    Route::post('/notes/{noteId}/chat', [ChatController::class, 'getOrCreateChatRoom']);
    Route::post('/chat-rooms/{chatRoomId}/messages', [ChatController::class, 'sendMessage']);
    Route::get('/chat-rooms/{chatRoomId}/messages', [ChatController::class, 'getMessages']);

    Route::get('/notifications/announcements', [NotificationController::class, 'getAllAnnouncement']);
    Route::get('/notifications/users/{id}', [NotificationController::class, 'getUserNotification']);

    // Route::prefix('/user')->group(function () {
    //     // Route::get(/{id}/notes, [...]);
    //     // Route::get(/{id}/reviews, [...]);
    // });

    // ...
});

Route::prefix('/admin')->middleware(['auth:sanctum', 'isAdmin'])->group(function () {

    Route::get('/reports', [AdminController::class, 'getAllReports']);

    Route::get('/notes-submission', [AdminController::class, 'getAllNotesSubmission']);
    Route::get('/notes-submission/handled', [AdminController::class, 'getAllHandledSubmission']);

    Route::get('/notes-submission/queue', [AdminController::class, 'getAllQueuSubmissions']);
    Route::get('/notes-submission/{id}/queue', [AdminController::class, 'getDetailQueuSubmissions']);
    Route::post('/notes-submission/{id}/queue', [AdminController::class, 'addSubmissionsToQueue']);
    Route::post('/notes-submission/{id}/handle', [AdminController::class, 'handleQueueSubmission']);

    Route::get('/users', [AdminController::class, 'getAllUsers']);
    Route::patch('/users/{id}/ban', [AdminController::class, 'banUser']);

    Route::post('/notifications/announcements', [NotificationController::class, 'createAnnouncement']);
    Route::post('/notifications/users/{id}/warnings', [NotificationController::class, 'createWarning']);
});
