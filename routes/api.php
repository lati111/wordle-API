<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;

Route::get('/auth/register', [AuthController::class, 'failure_wrong_method']);
Route::post('/auth/register', [AuthController::class, 'createUser'])->name("register");
Route::get('/auth/login', [AuthController::class, 'failure_wrong_method']);
Route::post('/auth/login', [AuthController::class, 'loginUser'])->name("login");
Route::get('/auth/login', [AuthController::class, 'failure_wrong_method']);
Route::post('/auth/getVerifyToken', [AuthController::class, 'getVerifyToken'])->name("auth.verify.start");



Route::get('/auth/fail', [AuthController::class, 'failure_no_token'])->name("auth.fail");
