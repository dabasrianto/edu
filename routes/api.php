<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CourseController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Routes moved to web.php to share session state
// Route::get('/courses', ...);
// Route::post('/courses/enroll', ...);
