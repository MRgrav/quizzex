<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Authentication Routes
Route::get('/login', \App\Livewire\Auth\Login::class)->name('auth.login');
Route::get('/register', \App\Livewire\Auth\InstituteRegister::class)->name('auth.register');
Route::get('/forgot-password', \App\Livewire\Auth\ForgotPassword::class)->name('password.request');
Route::get('/reset-password/{token}', \App\Livewire\Auth\ResetPassword::class)->name('password.reset');

Route::post('/logout', function () {
    auth()->logout();
    session()->invalidate();
    session()->regenerateToken();
    return redirect()->route('auth.login');
})->name('logout');

// Admin Routes
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', \App\Livewire\Admin\Dashboard::class)->name('dashboard');
    Route::get('/organizations', \App\Livewire\Admin\Organizations\Index::class)->name('organizations');
    Route::get('/organizations/{institute}', \App\Livewire\Admin\Organizations\View::class)->name('organizations.view');
    Route::get('/approvals', \App\Livewire\Admin\Approvals\Index::class)->name('approvals');
    Route::get('/quizzes', \App\Livewire\Shared\AllQuizzes::class)->name('quizzes');
    Route::get('/quizzes/create', \App\Livewire\Shared\CreateQuiz::class)->name('quiz.create');
    Route::get('/quizzes/{quiz}', \App\Livewire\Shared\ViewQuiz::class)->name('quizzes.view');
    Route::get('/quizzes/{quiz}/questions', \App\Livewire\Shared\ManageQuestions::class)->name('quizzes.questions');
    Route::get('/results', \App\Livewire\Admin\AllResults\Index::class)->name('results');
    Route::get('/quizzes/{quizAttempt}/result', \App\Livewire\Shared\QuizResult::class)->name('quizzes.result');
});

// Institute Routes
Route::middleware(['auth'])->prefix('organization')->name('organization.')->group(function () {
    Route::get('/dashboard', \App\Livewire\Organization\Dashboard\Index::class)->name('dashboard');
    Route::get('/participants', \App\Livewire\Organization\Participants\Index::class)->name('participants');
    Route::get('/quizzes', \App\Livewire\Shared\AllQuizzes::class)->name('quizzes');
    Route::get('/quizzes/create', \App\Livewire\Shared\CreateQuiz::class)->name('quiz.create');
    Route::get('/quizzes/{quiz}', \App\Livewire\Shared\ViewQuiz::class)->name('quizzes.view');
    Route::get('/quizzes/{quiz}/questions', \App\Livewire\Shared\ManageQuestions::class)->name('quizzes.questions');
    Route::get('/results', \App\Livewire\Organization\Results\Index::class)->name('results');
    Route::get('/quizzes/{quizAttempt}/result', \App\Livewire\Shared\QuizResult::class)->name('quizzes.result');
});

// Participant Routes
Route::middleware(['auth'])->prefix('participant')->name('participant.')->group(function () {
    Route::get('/dashboard', \App\Livewire\Participant\Dashboard::class)->name('dashboard');
    Route::get('/quizzes', \App\Livewire\Participant\AvailableQuizzes\Index::class)->name('quizzes');
    Route::get('/quizzes/{quiz}/attempt', \App\Livewire\Participant\TakeQuiz::class)->name('quizzes.attempt');
    Route::get('/quizzes/{quizAttempt}/result', \App\Livewire\Shared\QuizResult::class)->name('quizzes.result');
    Route::get('/results', \App\Livewire\Participant\MyResults\Index::class)->name('results');
});

// Default redirect
Route::get('/', function () {
    if (auth()->check()) {
        return match (auth()->user()->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'institute' => redirect()->route('organization.dashboard'),
            'participant' => redirect()->route('participant.dashboard'),
            default => redirect()->route('auth.login'),
        };
    }
    return redirect()->route('auth.login');
});