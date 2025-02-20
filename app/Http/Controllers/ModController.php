<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Contracts\ModControllerInterface;
use App\Models\Game;
use App\Models\Mod;
use App\Services\ModService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ModController implements ModControllerInterface
{
    private $modService;

    public function __construct(ModService $modService)
    {
    }

    public function browse(Request $request, Game $game): JsonResponse
    {
        // TODO: Implement browse() method.
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

    public function read(Request $request, Game $game, Mod $mod): JsonResponse
    {
        // TODO: Implement read() method.
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
