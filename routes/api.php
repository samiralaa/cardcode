<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CardController;

use App\Http\Controllers\CardLinkController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('register', 'register');
    Route::post('logout', 'logout');
    Route::post('refresh', 'refresh');


});
Route::post('password/email', [AuthController::class, 'sendResetLinkEmail']);
Route::post('password/reset', [AuthController::class, 'resetPassword']);
Route::post('/forgot-password', [AuthController::class, 'sendResetCode']);
Route::get('users', [AuthController::class, 'index']);

Route::get('user-cars/{id}',[CardController::class, 'userCard']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('cards', [CardController::class, 'index']);
    Route::get('cards/{id}', [CardController::class, 'show']);
    Route::post('cards', [CardController::class, 'store']);
    Route::get('my-cards',[CardController::class, 'myCard']);
    Route::post('cards/{id}', [CardController::class, 'update']);
    Route::delete('cards/{id}', [CardController::class, 'destroy']);
    Route::get('cards/slug/{slug}', [CardController::class, 'showBySlug']);
    Route::get('get-one-card/{id}',[CardController::class, 'getOne']);
  
    Route::post('detete-image/{id}',[CardController::class, 'deleteImageCard']);
    Route::post('update-image/{id}',[CardController::class, 'updateImageCard']);
});


Route::middleware('auth:sanctum')->group(function () {
    Route::get('cards-link', [CardLinkController::class, 'index']);
    Route::get('cards-link/{id}', [CardLinkController::class, 'show']);
    Route::post('cards-link', [CardLinkController::class, 'store']);
    Route::post('cards-link/{id}', [CardLinkController::class, 'update']);
    Route::delete('cards-link/{id}', [CardLinkController::class, 'destroy']);
    Route::get('cards-link/slug/{slug}', [CardLinkController::class, 'showBySlug']);

});

Route::middleware('auth:sanctum')->group(function () {
    Route::delete('delete-users/{id}', [\App\Http\Controllers\UserController::class, 'delete']);
    Route::post('update-user', [\App\Http\Controllers\UserController::class, 'update']);
    Route::get('users/{id}', [\App\Http\Controllers\UserController::class, 'index']);
});





