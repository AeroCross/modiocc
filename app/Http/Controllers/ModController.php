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
     * @param Game $game
     * @return JsonResponse
     */
    public function create(Request $request, Game $game)
    {
        // TODO: Implement create() method.
    }

    public function read(Request $request, string $gameId, string $modId): JsonResponse
    {
        return (new ModResource($this->modService->find($request)))->response();
    }

    public function update(Request $request, Game $game, Mod $mod): JsonResponse
    {
        // TODO: Implement update() method.
    }

    public function delete(Request $request, Game $game, Mod $mod): JsonResponse
    {
        // TODO: Implement delete() method.
    }
}
