<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\OrderController;

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

Route::middleware('guest:sanctum')->post('login', \App\Http\Controllers\Api\AuthController::class);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('order', [OrderController::class, 'store']);
    Route::delete('order/{id}', [OrderController::class, 'delete']);
    Route::get('order', [OrderController::class, 'index']);
    Route::get('discount/{id}', [OrderController::class, 'discount']);
});
