<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GameController;
use App\Models\Game;

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

/**
 * Authenticated routes
 */
Route::middleware('auth:sanctum')->controller(GameController::class)->group(function () {
    Route::post('/games', 'create')->name('create-game');
});

/**
 * Unauthenticated routes
 *
 * Note: These routes are set this way under the assumption that these endpoints are for end users, not only for creators.
 * The instructions say "any user", not "any authenticated user", so I'm rolling with this since it makes more practical
 * sense to want anyone to browse both the games available and their mods for them to subscribe.
 */
Route::get('/games', [GameController::class, 'browse'])->name('browse');

/**
 * We're using "id" as the parameter here instead of "game" to avoid Laravel's implicit model binding.
 * See: https://laravel.com/docs/11.x/routing#implicit-binding
 *
 * The reason I'm doing this is to be able to control better where the "missing" logic lies. Ideally, this resides
 * in the GameService, instead of having to write closures in the routes file.
 *
 * Using implicit bindings is a lot simpler, but it does break our pattern. This forces us to change the Interface of
 * GameController, but it sounds like a reasonable tradeoff for consistency and encapsulation.
 */
Route::get('/games/{id}', [GameController::class, 'read'])->name('find-game');
