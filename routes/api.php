<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;

Route::get('/auth/register', [AuthController::class, 'failure_wrong_method']);
Route::post('/auth/register', [AuthController::class, 'createUser'])->name("register");
Route::get('/auth/login', [AuthController::class, 'failure_wrong_method']);
Route::post('/auth/login', [AuthController::class, 'loginUser'])->name("login");
Route::get('/auth/login', [AuthController::class, 'failure_wrong_method']);

Route::post('/auth/forgot', [AuthController::class, 'passwordForgot'])->name("auth.forgot");
Route::post('/auth/reset', [AuthController::class, 'resetPassword'])->name("auth.reset");

Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verify'])->middleware(['signed'])->name('verification.verify');

Route::get('/auth/fail', [AuthController::class, 'failure_no_token'])->name("auth.fail");
