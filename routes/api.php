<?php

use App\Http\Controllers\InstituteController;
use App\Http\Controllers\ParticipantController;
use App\Http\Controllers\QuizController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::name('api.')->group(function () {
    require __DIR__ . '/auth.php';
});

// Authenticated user routes
Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

// Role-based user routes
Route::middleware(['auth:sanctum', 'isAdmin'])->get('/admin/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['auth:sanctum', 'isInstitute'])->get('/institute/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['auth:sanctum', 'isParticipant'])->get('/participant/user', function (Request $request) {
    return $request->user();
});

/*
|--------------------------------------------------------------------------
| Institute Routes
|--------------------------------------------------------------------------
*/

// Public institute registration
Route::post('/institutes/register', [InstituteController::class, 'register']);

// Admin-only institute management routes
Route::middleware(['auth:sanctum', 'isAdmin'])->prefix('institutes')->name('institutes.')->group(function () {
    Route::get('/pending', [InstituteController::class, 'pending'])->name('pending');
    Route::get('/', [InstituteController::class, 'index'])->name('index');
    Route::post('/{institute}/approve', [InstituteController::class, 'approve'])->name('approve');
    Route::post('/{institute}/reject', [InstituteController::class, 'reject'])->name('reject');
});

/*
|--------------------------------------------------------------------------
| Participant Routes (Institute Admin Only)
|--------------------------------------------------------------------------
*/

// Institute admin can create and manage participants
Route::middleware(['auth:sanctum', 'isInstitute'])->prefix('participants')->name('participants.')->group(function () {
    Route::post('/', [ParticipantController::class, 'store'])->name('store');
    Route::get('/', [ParticipantController::class, 'index'])->name('index');
    Route::get('/{participant}', [ParticipantController::class, 'show'])->name('show');
});

/*
|--------------------------------------------------------------------------
| Quiz Routes
|--------------------------------------------------------------------------
*/

// List and show active quizzes (visible to all authenticated users)
Route::middleware(['auth:sanctum'])->prefix('quizzes')->name('quizzes.')->group(function () {
    Route::get('/', [QuizController::class, 'index'])->name('index');
    Route::get('/{quiz}', [QuizController::class, 'show'])->name('show');
});

// Create quiz - admin or institute only (authorization handled in StoreQuizRequest)
Route::middleware(['auth:sanctum'])->post('/quizzes', [QuizController::class, 'store'])->name('quizzes.store');

// Update quiz - admin or institute only (authorization handled in UpdateQuizRequest)
Route::middleware(['auth:sanctum'])->match(['put', 'patch'], '/quizzes/{quiz}', [QuizController::class, 'update'])->name('quizzes.update');

/*
|--------------------------------------------------------------------------
| Quiz Attempt Routes
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\QuizAttemptController;

// Quiz attempt management (participants can take quizzes)
Route::middleware(['auth:sanctum'])->prefix('quiz-attempts')->name('quiz-attempts.')->group(function () {
    Route::post('/', [QuizAttemptController::class, 'start'])->name('start');
    Route::get('/', [QuizAttemptController::class, 'index'])->name('index');
    Route::get('/{quizAttempt}', [QuizAttemptController::class, 'show'])->name('show');
    Route::post('/{quizAttempt}/submit', [QuizAttemptController::class, 'submit'])->name('submit');
});

/*
|--------------------------------------------------------------------------
| Results Viewing Routes
|--------------------------------------------------------------------------
*/

// Participant - view own results
Route::middleware(['auth:sanctum', 'isParticipant'])->get('/results/my-results', [QuizAttemptController::class, 'myResults'])->name('results.my');

// Institute admin - view institute results
Route::middleware(['auth:sanctum', 'isInstitute'])->get('/results/institute-results', [QuizAttemptController::class, 'instituteResults'])->name('results.institute');

// System admin - view all results
Route::middleware(['auth:sanctum', 'isAdmin'])->get('/results/all-results', [QuizAttemptController::class, 'allResults'])->name('results.all');
