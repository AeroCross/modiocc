<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Contracts\ModControllerInterface;
use App\Http\Resources\ModCollection;
use App\Http\Resources\ModResource;
use App\Models\Game;
use App\Models\Mod;
use App\Services\ModService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ModController implements ModControllerInterface
{
    public function __construct(protected ModService $modService) {}

    public function browse(Request $request): ModCollection
    {
        return new ModCollection(
            $this->modService->getAllPaginated($request)
        );
    }

    /**
     * Create a mod.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request)
    {
        $mod = $this->modService->create($request);

        // Already exists
        if ($mod === false) {
            return response()->json(['message' => 'Mod already exists for game.'], 403);
        }

        if ($mod) {
            return (new ModResource($mod))
                ->response()
                ->setStatusCode(201);
        }

        return response()->json(null, 422);
    }

    public function read(Request $request, string $gameId, string $modId): JsonResponse
    {
        return (new ModResource($this->modService->find($request)))->response();
    }

    public function update(Request $request, Game $game, Mod $mod): JsonResponse
    {
        // TODO: Implement update() method.
    }

    /**
     * Deletes a game.
     *
     * This is a hard deletion.
     *
     * @param Request $request
     * @param string $gameId The ID of the game.
     * @param string $modId The ID of the mod.
     * @return JsonResponse
     */
    public function delete(Request $request, string $gameId, string $modId): JsonResponse
    {
        $game = $this->modService->delete($gameId, $request);

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
