<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->group(function () {

    Route::prefix('user')->group(function () {
        Route::get('/{id?}', [UserController::class, 'user']);
    });

    Route::prefix('category')->group(function () {
        Route::post('/', [CategoryController::class, 'store']);
        Route::get('/{id?}', [CategoryController::class, 'get']);
    });


});
