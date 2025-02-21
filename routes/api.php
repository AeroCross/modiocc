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
Route::middleware('auth:sanctum')->controller(GameController::class)->group(function () {
    Route::post('/games', 'create')->name('create-game');
});

// Unauthenticated routes
// Note: Under the assumption that the `/browse` endpoint is for end-users, it makes sense
// for them not to be behind authentication, since you want them to browse both the games available and their mods for
// them to subscribe.
Route::get('/games', [GameController::class, 'browse'])->name('browse');
