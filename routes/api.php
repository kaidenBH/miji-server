<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LinkController;

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
Route::prefix('user')->group(function () {
    Route::post('/login', [UserController::class, 'login']);
    Route::post('/register', [UserController::class, 'register']);
    Route::put('updateUser/{userId}', [UserController::class, 'update']);
    Route::delete('deleteUser/{userId}', [UserController::class, 'destroy']);
});
Route::prefix('link')->group(function () {
    Route::post('/createLink', [LinkController::class, 'createLink']);
    Route::get('/getLink/{short_link}', [LinkController::class, 'getLink']);
    Route::delete('deleteLink/{short_link}', [LinkController::class, 'destroy']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
