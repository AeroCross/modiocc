<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GameController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/tokens/create', function (Request $request) {
    $token = $request->user()->createToken($request->token_name);

    return ['token' => $token->plainTextToken];
});

// Authenticated routes
// TODO: we do not want all the controller routes to be defined here, we're just overwriting them at the end atm
Route::middleware('auth:sanctum')->controller(GameController::class)->group(function () {
    Route::get('/games', 'browse')->name('browse');
});

// Unauthenticated routes
Route::get('/games', [GameController::class, 'browse'])->name('browse');
