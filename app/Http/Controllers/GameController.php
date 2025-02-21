<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Contracts\GameControllerInterface;
use App\Models\Game;
use App\Services\GameService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GameController extends Controller implements GameControllerInterface
{
    private GameService $gameService;

    public function __construct(GameService $gameService) {}

    public function browse(Request $request): JsonResponse
    {
        // TODO: for some reason, Laravel isn't immediately converting this Eloquent model into a JSON response.
        // This should happen: https://laravel.com/docs/11.x/responses#eloquent-models-and-collections
        // It's not the Sanctum middleware, and it's not the custom middleware either.
        return response()->json(Game::paginate(10));
    }

    public function create(Request $request): JsonResponse
    {
        // TODO: Implement create() method.
    }

    public function read(Request $request, Game $game): JsonResponse
    {
        // TODO: Implement read() method.
    }

    public function update(Request $request, Game $game): JsonResponse
    {
        // TODO: Implement update() method.
    }

    public function delete(Request $request, Game $game): JsonResponse
    {
        // TODO: Implement delete() method.
    }
}
