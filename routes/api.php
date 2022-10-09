<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ExerciseController;
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
        Route::get('/{id?}', [UserController::class, 'get']);
    });

    Route::prefix('categories')->group(function () {
        Route::get('/{id?}', [CategoryController::class, 'get']);
        Route::post('/', [CategoryController::class, 'store']);
    });

    Route::prefix('exercises')->group(function () {
        Route::get('/{id?}', [ExerciseController::class, 'get']);
        Route::post('/', [ExerciseController::class, 'store']);
        Route::put('/{id}', [ExerciseController::class, 'update']);
        Route::delete('/{id}', [ExerciseController::class, 'delete']);
    });


});
