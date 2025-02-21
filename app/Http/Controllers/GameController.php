<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Contracts\GameControllerInterface;
use App\Models\Game;
use App\Repositories\GameRepository;
use App\Services\GameService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GameController extends Controller implements GameControllerInterface
{
    private GameService $gameService;
    private GameRepository $gameRepository;

    public function __construct(GameService $gameService, GameRepository $gameRepository)
    {
        $this->gameService = $gameService ?? new GameService;
        $this->gameRepository = $gameRepository ?? new GameRepository();
    }

    public function browse(Request $request): JsonResponse
    {
        // TODO: for some reason, Laravel isn't immediately converting this Eloquent model into a JSON response.
        // This should happen: https://laravel.com/docs/11.x/responses#eloquent-models-and-collections
        // It's not the Sanctum middleware, and it's not the custom middleware either.
        return response()->json(
            $this->gameRepository->paginate(10)
        );
    }

    public function create(Request $request): JsonResponse
    {
        $user = $request->user();

        $validatedData = $request->validate([
            'name' => ['required', 'unique:App\Models\Game,name'],
        ]);

        $game = $this->gameRepository->create(
            [
                'user_id' => $user->id,
                'name' => $validatedData['name'],
            ]
        );

        if ($game) {
            return response()->json($game, 201);
        }

        return response()->json(null, 422); // This should be unreachable due to the validate() call
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
