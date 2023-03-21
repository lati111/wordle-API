<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ClientController;
use App\Http\Controllers\API\WordleController;

//| authetication
Route::get('/auth/{client_key}/register', [AuthController::class, 'failure_wrong_method']);
Route::post('/auth/{client_key}/register', [AuthController::class, 'createUser'])->name("register");
Route::post('/auth/{client_key}/login', [AuthController::class, 'loginUser'])->name("login");
Route::get('/auth/{client_key}/login', [AuthController::class, 'failure_wrong_method']);

Route::post('/auth/{client_key}/forgot', [AuthController::class, 'passwordForgot'])->name("auth.forgot");
Route::post('/auth/{client_key}/reset', [AuthController::class, 'resetPassword'])->name("auth.reset");

Route::post('/auth/refresh', [AuthController::class, 'refreshToken'])
    ->middleware(['auth:sanctum', 'ability:refresh'])
    ->name("auth.refresh");

// Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verify'])
//     ->middleware(['signed'])
//     ->name('verification.verify');
Route::get('/auth/fail/no_token', [AuthController::class, 'failure_no_token'])->name("auth.fail.token.none");
Route::get('/auth/fail/false_token', [AuthController::class, 'failure_false_token'])->name("auth.fail.token.false");


//| client
Route::post('/client/new', [ClientController::class, 'newClient'])
    ->name('client.new');


//| wordle
Route::post('/wordle/{client_key}/newgame', [WordleController::class, 'newGame'])
    ->middleware(['auth:sanctum', 'ability:auth'])
    ->name('wordle.new');
Route::post('/wordle/{client_key}/setscore/{session_key}', [WordleController::class, 'setScore'])
    ->middleware(['auth:sanctum', 'ability:auth'])
    ->name('wordle.setscore');
Route::post('/wordle/{client_key}/currentscore/{session_uuid}', [WordleController::class, 'getScore'])
    ->middleware(['auth:sanctum', 'ability:auth'])
    ->name('wordle.topscore');
Route::post('/wordle/{client_key}/topscore', [WordleController::class, 'topScore'])
    ->middleware(['auth:sanctum', 'ability:auth'])
    ->name('wordle.topscore');

