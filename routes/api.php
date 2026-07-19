<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\QuizController;
use App\Http\Controllers\Api\RoomController;
use App\Http\Controllers\Api\AnswerController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// Public routes (walang auth)
Route::post('/teacher/login', [AuthController::class, 'teacherLogin']);
Route::post('/room/join', [RoomController::class, 'join']);

// Teacher routes (may auth)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/teacher/logout', [AuthController::class, 'logout']);
    Route::get('/teacher/quizzes', [QuizController::class, 'index']);
    Route::post('/room/create', [RoomController::class, 'create']);
    Route::post('/room/{code}/start', [RoomController::class, 'start']);
    Route::post('/room/{code}/end', [RoomController::class, 'end']);
    Route::get('/room/{code}/results', [RoomController::class, 'results']);
    Route::post('/essay/{id}/grade', [AnswerController::class, 'gradeEssay']);
});

// Student routes (walang account kailangan)
Route::post('/student/answer', [AnswerController::class, 'submit']);
Route::post('/student/answer-one', [AnswerController::class, 'submitOne']);
Route::post('/student/finish', [AnswerController::class, 'finish']);
Route::post('/participant/{id}/activity', [RoomController::class, 'reportActivity']);
Route::get('/room/{code}/questions', [RoomController::class, 'getQuestions']);
Route::get('/room/{code}/leaderboard', [RoomController::class, 'leaderboard']);