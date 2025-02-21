<?php

namespace App\Http\Controllers\Contracts;

use App\Models\Game;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * GameControllerInterface.
 */
interface GameControllerInterface
{
    /**
     * Browse Games.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function browse(Request $request): JsonResponse;

    /**
     * Create game.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request): JsonResponse;

    /**
     * Read/view a game.
     *
     * @param Request $request
     * @param Game $game
     * @return JsonResponse
     */
    public function read(Request $request): JsonResponse;

    /**
     * Update a game.
     *
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function update(Request $request, string $id): JsonResponse;

    /**
     * Delete a game.
     *
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function delete(Request $request, string $id): JsonResponse;
}
