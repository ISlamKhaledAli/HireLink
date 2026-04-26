<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Auth;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);
    
    Route::get('/verify-auth', function (Request $request) {
        return response()->json([
            'message' => 'Auth is working perfectly!',
            'user' => clone $request->user()->load('roles'),
            'role_names' => $request->user()->getRoleNames()
        ]);
    });
});
