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

    public function __construct(GameService $gameService)
    {
        $this->gameService = $gameService ?? new GameService();
    }

    /** Provides a paginated list of all games for authenticated and unauthenticated users.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function browse(Request $request): JsonResponse
    {
        return response()->json(
            $this->gameService->getAllPaginated()
        );
    }

    /** Creates a new game for the currently authenticated user.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request): JsonResponse
    {
        $game = $this->gameService->create($request);

        if ($game) {
            return response()->json($game, 201);
        }

        return response()->json(null, 422);
    }


    /**
     * Gets the data of a single game.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function read(Request $request): JsonResponse
    {
        return response()->json(
            $this->gameService->find($request)
        );
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
