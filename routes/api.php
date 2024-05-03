<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\BookController;

Route::get('/home', [ClientController::class, 'home'])->name('home');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::post('/register', [AuthController::class, 'register']);
Route::get('/me', [AuthController::class, 'me']);

Route::group(['prefix' => 'store', 'middleware' => 'auth:api'], function () {
    Route::get('/all', [StoreController::class, 'all']);
    Route::post('/create', [StoreController::class, 'create']);
    Route::get('/{id}', [StoreController::class, 'show']);
    Route::put('/{id}/update', [StoreController::class, 'update']);
    Route::delete('/delete', [StoreController::class, 'delete']);
});

Route::group(['prefix' => 'book', 'middleware' => 'auth:api'], function () {
    Route::get('/all', [BookController::class, 'all']);
    Route::post('/create', [BookController::class, 'create']);
    Route::get('/{id}', [BookController::class, 'show']);
    Route::put('/{id}/update', [BookController::class, 'update']);
    Route::delete('/delete', [BookController::class, 'delete']);
});