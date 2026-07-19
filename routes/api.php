<?php
 
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PelajarController;
use App\Http\Controllers\MentorController;
use App\Http\Controllers\MitraController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\SubmissionController;
 
/*
|--------------------------------------------------------------------------
| API Routes - KerjaIn v2
|--------------------------------------------------------------------------
*/
 
Route::middleware('web')->group(function () {
 
    // ── Public ────────────────────────────────────────────────────────────
    Route::post('/register',     [AuthController::class, 'register']);
    Route::post('/login',        [AuthController::class, 'login']);
    Route::get('/session',       [AuthController::class, 'getSession']);
    Route::post('/logout',       [AuthController::class, 'logout']);

    // ── Shared (semua role yang login) ────────────────────────────────────
    Route::middleware('auth.custom')->group(function () {
        Route::post('/change-password', [AuthController::class, 'changePassword']);
        Route::post('/upload-foto',     [AuthController::class, 'uploadFoto']);
        
        // Notifications (Gamifikasi Fase 4)
        Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index']);
        Route::post('/notifications/{id}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead']);


    });

    // ── Browse Program (publik, tapi login recommended) ───────────────────
    Route::get('/programs',       [ProgramController::class, 'browse']);
    Route::get('/programs/{id}',  [ProgramController::class, 'detail']);
 
    // ── Pelajar ───────────────────────────────────────────────────────────
    Route::middleware('role:pelajar')->prefix('pelajar')->group(function () {
        Route::get('/profile',   [PelajarController::class, 'show']);
        Route::post('/update',   [PelajarController::class, 'update']);
        Route::post('/delete',   [PelajarController::class, 'destroy']);
        Route::get('/dashboard', [PelajarController::class, 'dashboard']);
        Route::get('/leaderboard', [PelajarController::class, 'leaderboard']);

        // Enrollment
        Route::post('/enroll/{programId}',   [EnrollmentController::class, 'enroll']);
        Route::get('/enrollments',           [EnrollmentController::class, 'myEnrollments']);
        Route::get('/enrollments/{id}',      [EnrollmentController::class, 'showEnrollment']);

        // Submit task
        Route::post('/submit', [SubmissionController::class, 'submit']);

        // Pelajar: Certificates & Portfolios
        Route::get('/certificates', [PelajarController::class, 'certificates']);
        Route::get('/portfolios',   [PelajarController::class, 'portfolios']);
    });
 
    // ── Mentor ────────────────────────────────────────────────────────────
    Route::middleware('role:mentor')->prefix('mentor')->group(function () {
        Route::get('/profile',   [MentorController::class, 'show']);
        Route::post('/update',   [MentorController::class, 'update']);
        Route::post('/delete',   [MentorController::class, 'destroy']);
        Route::get('/dashboard', [MentorController::class, 'dashboard']);

        // Review submissions
        Route::get('/submissions',             [SubmissionController::class, 'mentorSubmissions']);
        Route::post('/submissions/{id}/review', [SubmissionController::class, 'review']);

        // Mentor: Explore & Apply
        Route::get('/explore-programs',               [MentorController::class, 'explorePrograms']);
        Route::post('/apply/{programId}',             [MentorController::class, 'applyToProgram']);
        Route::post('/cancel-application/{programId}',[MentorController::class, 'cancelApplication']);
        Route::get('/my-applications',                [MentorController::class, 'myApplications']);
    });
 
    // ── Mitra ─────────────────────────────────────────────────────────────
    Route::middleware('role:mitra')->prefix('mitra')->group(function () {
        Route::get('/profile',   [MitraController::class, 'show']);
        Route::post('/update',   [MitraController::class, 'update']);
        Route::post('/delete',   [MitraController::class, 'destroy']);
        Route::get('/dashboard', [MitraController::class, 'dashboard']);
        Route::get('/dashboard-charts', [MitraController::class, 'dashboardCharts']);
        Route::get('/mentors',   [MitraController::class, 'mentors']);
        Route::get('/candidates', [MitraController::class, 'searchCandidates']);
        Route::post('/upload-logo', [MitraController::class, 'uploadLogo']);
        Route::get('/portfolio-tasks/{portfolioId}', [MitraController::class, 'getPortfolioTasks']);

        // Program CRUD
        Route::get('/programs',                        [ProgramController::class, 'index']);
        Route::post('/programs',                       [ProgramController::class, 'store']);
        Route::get('/programs/{id}',                   [ProgramController::class, 'show']);
        Route::post('/programs/{id}/update',           [ProgramController::class, 'update']);
        Route::post('/programs/{id}/publish',          [ProgramController::class, 'publish']);
        Route::post('/programs/{id}/close',            [ProgramController::class, 'close']);
        Route::post('/programs/{id}/delete',           [ProgramController::class, 'destroy']);
        Route::post('/programs/{id}/assign-mentor',    [ProgramController::class, 'assignMentor']);
        Route::post('/programs/{id}/upload-cover',     [ProgramController::class, 'uploadCover']);
        Route::get('/programs/{id}/leaderboard',       [ProgramController::class, 'leaderboard']);

        // Task CRUD
        Route::post('/programs/{programId}/tasks',     [TaskController::class, 'store']);
        Route::post('/tasks/{id}/update',              [TaskController::class, 'update']);
        Route::post('/tasks/{id}/delete',              [TaskController::class, 'destroy']);

        // Mitra: Mentor Applications Management
        Route::get('/mentor-applications',              [ProgramController::class, 'mentorApplications']);
        Route::post('/mentor-applications/{id}/review', [ProgramController::class, 'reviewMentorApplication']);
    });
 
});