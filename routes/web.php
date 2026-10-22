<?php

use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GameSessionController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\SoloController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public
|--------------------------------------------------------------------------
*/
Route::get('/', fn() => view('adventure.landing'))->name('landing');
Route::get('/login', fn() => view('adventure.auth.login'))->name('login');
Route::post('/login', fn() => redirect()->route('dashboard'))->name('login.post');
Route::get('/register', fn() => view('adventure.auth.register'))->name('register');
Route::post('/register', fn() => redirect()->route('login'))->name('register.post');
Route::post('/logout', fn() => auth()->logout())->name('logout');
Route::get('/join', fn() => view('adventure.join'))->name('join');
Route::get('/about', fn() => view('adventure.about'))->name('about');
Route::get('/guide', fn() => view('adventure.guide'))->name('guide');

/*
|--------------------------------------------------------------------------
| Authenticated
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', fn() => view('adventure.profile'))->name('profile');
    Route::get('/avatar', \App\Livewire\Player\AvatarPicker::class)->name('avatar');
    Route::get('/history', [SoloController::class, 'history'])->name('history');
    Route::get('/results/{attemptId}', [SoloController::class, 'results'])->name('results');
    Route::get('/leaderboard', [SoloController::class, 'leaderboard'])->name('leaderboard');

    // Solo Practice
    Route::get('/solo/{quizId}', [SoloController::class, 'start'])->name('solo.start');
    Route::get('/play', [SoloController::class, 'play'])->name('play');

    // Live Quiz — Room
    Route::post('/rooms', [RoomController::class, 'create'])->name('rooms.create');
    Route::get('/room/{code}', [RoomController::class, 'show'])->name('room.show');
    Route::post('/room/{code}/join', [RoomController::class, 'join'])->name('room.join');
    Route::get('/room/{code}/start', [RoomController::class, 'start'])->name('room.start');

    // Live Quiz — Session
    Route::get('/game/{sessionId}', [GameSessionController::class, 'index'])->name('game.session');
    Route::post('/game/{sessionId}/start', [GameSessionController::class, 'start'])->name('game.start');
    Route::post('/game/{sessionId}/answer', [GameSessionController::class, 'submitAnswer'])->name('game.answer');
    Route::post('/game/{sessionId}/finish', [GameSessionController::class, 'finish'])->name('game.finish');
});

/*
|--------------------------------------------------------------------------
| Admin
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', fn() => view('adventure.admin.dashboard'))->name('dashboard');
    Route::get('/users', fn() => view('adventure.admin.users'))->name('users');
    Route::get('/settings', fn() => view('adventure.admin.settings'))->name('settings');
    Route::get('/audit-logs', fn() => view('adventure.admin.audit'))->name('audit-logs');

    // Subjects
    Route::get('/subjects', [SubjectController::class, 'index'])->name('subjects');
    Route::post('/subjects', [SubjectController::class, 'store'])->name('subjects.store');
    Route::get('/subjects/{id}/edit', [SubjectController::class, 'edit'])->name('subjects.edit');
    Route::put('/subjects/{id}', [SubjectController::class, 'update'])->name('subjects.update');
    Route::delete('/subjects/{id}', [SubjectController::class, 'destroy'])->name('subjects.destroy');

    // Questions
    Route::get('/questions', [QuestionController::class, 'index'])->name('questions');
    Route::get('/questions/create', [QuestionController::class, 'create'])->name('questions.create');
    Route::post('/questions', [QuestionController::class, 'store'])->name('questions.store');
    Route::get('/questions/{question}/edit', [QuestionController::class, 'edit'])->name('questions.edit');
    Route::put('/questions/{question}', [QuestionController::class, 'update'])->name('questions.update');
    Route::delete('/questions/{question}', [QuestionController::class, 'destroy'])->name('questions.destroy');
    Route::post('/questions/{question}/archive', [QuestionController::class, 'archive'])->name('questions.archive');
    Route::post('/questions/{question}/publish', [QuestionController::class, 'publish'])->name('questions.publish');
    Route::post('/questions/{question}/duplicate', [QuestionController::class, 'duplicate'])->name('questions.duplicate');
    Route::post('/questions/bulk', [QuestionController::class, 'bulk'])->name('questions.bulk');
    Route::get('/questions/export', [QuestionController::class, 'export'])->name('questions.export');
    Route::post('/questions/import', [QuestionController::class, 'import'])->name('questions.import');

    Route::get('/quizzes', fn() => view('adventure.admin.quizzes'))->name('quizzes');
    Route::get('/rooms', fn() => view('adventure.admin.rooms'))->name('rooms');
    Route::get('/reports', fn() => view('adventure.admin.reports'))->name('reports');
});

/*
|--------------------------------------------------------------------------
| Teacher
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:teacher'])->prefix('teacher')->name('teacher.')->group(function () {
    Route::get('/', fn() => view('adventure.dashboard.teacher'))->name('dashboard');
    Route::get('/subjects', fn() => view('adventure.teacher.subjects'))->name('subjects');
    Route::get('/quizzes', fn() => view('adventure.teacher.quizzes'))->name('quizzes');
});
