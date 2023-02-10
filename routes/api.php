<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/auth/register', [AuthController::class, 'failure_wrong_method']);
Route::post('/auth/register', [AuthController::class, 'createUser'])->name("register");
Route::post('/auth/login', [AuthController::class, 'loginUser'])->name("login");
Route::get('/auth/fail', [AuthController::class, 'failure_no_token'])->name("auth.fail");
