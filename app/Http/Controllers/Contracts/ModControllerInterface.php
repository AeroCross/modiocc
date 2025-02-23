<?php

namespace App\Http\Controllers\Contracts;

use App\Http\Resources\ModCollection;
use App\Models\Game;
use App\Models\Mod;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * GameControllerInterface.
 */
interface ModControllerInterface
{
    /**
     * Browse Mods.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function browse(Request $request): JsonResponse;

    /**
     * Create a mod.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request): JsonResponse;

    /**
     * Read/view a mod.
     *
     * @param Request $request
     * @param Game $game
     * @param Mod $mod
     * @return JsonResponse
     */
    public function read(Request $request): JsonResponse;

    /**
     * Update a mod.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request): JsonResponse;

    /**
     * Delete a mod.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function delete(Request $request): JsonResponse;
}
