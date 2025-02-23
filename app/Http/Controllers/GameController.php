<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Contracts\GameControllerInterface;
use App\Http\Resources\GameCollection;
use App\Http\Resources\GameResource;
use App\Services\GameService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GameController extends Controller implements GameControllerInterface
{
    public function __construct(protected GameService $gameService) {}

    /** Provides a paginated list of all games for authenticated and unauthenticated users.
     *
     * @param Request $request
     * @return GameCollection
     */
    public function browse(Request $request): JsonResponse
    {
        return (new GameCollection(
            $this->gameService->getAllPaginated()
        ))->response();
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
            return (new GameResource($game))
                ->response()
                ->setStatusCode(201);
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
        $game = $this->gameService->find($request);

        if ($game) {
            return (new GameResource($game))->response();
        }

        return response()->json(null, 404);
    }

    /**
     * Updates the data of a single game.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request, string $gameId): JsonResponse
    {
        $game = $this->gameService->update($gameId, $request);

        if ($game === null) {
            return response()->json(['message' => 'Not found.'], 404);
        }

        if ($game === false) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        return (new GameResource($game))->response();
    }

    /**
     * Deletes a game.
     *
     * This is a hard deletion.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function delete(Request $request, string $gameId): JsonResponse
    {
        $game = $this->gameService->delete($gameId, $request);

        if ($game === null) {
            return response()->json(['message' => 'Not found.'], 404);
        }

        if ($game === false) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        if ($game > 0) {
            return response()->json(['message' => 'Success.'], 204);
        }

        return response()->json(null, 500);
    }
}
