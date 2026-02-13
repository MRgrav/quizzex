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
    Route::get('/approvals', \App\Livewire\Admin\Approvals\Index::class)->name('approvals');
    Route::get('/quizzes', \App\Livewire\Shared\AllQuizzes::class)->name('quizzes');
    Route::get('/quizzes/create', \App\Livewire\Shared\CreateQuiz::class)->name('quizzes.create');
    Route::get('/quizzes/{quiz}', \App\Livewire\Admin\AllQuizzes\ViewQuiz::class)->name('quizzes.view');
    Route::get('/all-results', \App\Livewire\Admin\AllResults\Index::class)->name('all-results');
    Route::get('/results', function () {
        return 'Admin Results - Coming Soon';
    })->name('results');
});

// Institute Routes
Route::middleware(['auth'])->prefix('organization')->name('organization.')->group(function () {
    Route::get('/dashboard', \App\Livewire\Organization\Dashboard\Index::class)->name('dashboard');
    Route::get('/participants', \App\Livewire\Organization\Participants\Index::class)->name('participants');
    Route::get('/quizzes', \App\Livewire\Shared\AllQuizzes::class)->name('my-quizzes');
    Route::get('/quizzes/create', \App\Livewire\Shared\CreateQuiz::class)->name('quizzes.create');
    Route::get('/results', \App\Livewire\Organization\Results\Index::class)->name('results');
});

// Participant Routes
Route::middleware(['auth'])->prefix('participant')->name('participant.')->group(function () {
    Route::get('/dashboard', \App\Livewire\Participant\Dashboard::class)->name('dashboard');
    Route::get('/quizzes', function () {
        return 'My Quizzes - Coming Soon';
    })->name('quizzes');
    Route::get('/results', function () {
        return 'My Results - Coming Soon';
    })->name('results');
});

// Default redirect
Route::get('/', function () {
    if (auth()->check()) {
        return match (auth()->user()->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'institute' => redirect()->route('institute.dashboard'),
            'participant' => redirect()->route('participant.dashboard'),
            default => redirect()->route('auth.login'),
        };
    }
    return redirect()->route('auth.login');
});