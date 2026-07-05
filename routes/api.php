<?php

use App\Http\Controllers\FavoriteLawController;
use Illuminate\Http\Request;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('web')->group(function () {
    Route::get('/favorites', [FavoriteLawController::class, 'index']);
    Route::post('/favorites', [FavoriteLawController::class, 'store']);
    Route::delete('/favorites/{law_id}', [FavoriteLawController::class, 'destroy']);
});
