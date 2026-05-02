<?php
use App\Http\Controllers\CommentController;
use App\Http\Controllers\AdminJobController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\Auth\LinkedInController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\AnalyticsController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/auth/linkedin/redirect', [LinkedInController::class, 'redirect']);
Route::get('/auth/linkedin/callback', [LinkedInController::class, 'callback']);

Route::get('/jobs', [JobController::class, 'index']);
Route::get('/jobs/{job}', [JobController::class, 'show']);

Route::post('/payments/webhook', [PaymentController::class, 'webhook']);
Route::post('/jobs/{job}/view', [AnalyticsController::class, 'recordView']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::post('/jobs', [JobController::class, 'store']);
    Route::put('/jobs/{job}', [JobController::class, 'update']);
    Route::delete('/jobs/{job}', [JobController::class, 'destroy']);

    Route::get('/verify-auth', function (Request $request) {
        return response()->json([
            'message' => 'Auth is working perfectly!',
            'user' => clone $request->user()->load('roles'),
            'role_names' => $request->user()->getRoleNames(),
        ]);
    });

    // Applications Module
    Route::post('/applications', [ApplicationController::class, 'apply']);
    Route::delete('/applications/{application}', [ApplicationController::class, 'cancel']);
    Route::put('/applications/{application}', [ApplicationController::class, 'updateStatus']);
    Route::get('/applications', [ApplicationController::class, 'index']);
});

Route::middleware(['auth:sanctum', 'admin'])->group(function () {
     Route::post('/admin/jobs/{job}/approve', [AdminJobController::class, 'approve']);
     Route::post('/admin/jobs/{job}/reject', [AdminJobController::class, 'reject']);

     Route::get('/admin/dashboard', [AdminJobController::class, 'dashboard']);
});

Route::get('/jobs/{job}/comments', [CommentController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/comments', [CommentController::class, 'store']);
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy']);
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/payments/initiate/{application}', [PaymentController::class, 'initiate']);
    Route::get('/payments/{application}', [PaymentController::class, 'status']);

    Route::get('/jobs/{job}/analytics', [AnalyticsController::class, 'analytics']);
});
