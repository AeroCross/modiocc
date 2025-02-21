<?php

namespace App\Http\Controllers\Contracts;

use App\Http\Resources\GameCollection;
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
     * NOTE: GameCollection extends ResourceCollection which extends JsonResponse.
     * As far as my research goes, PHP should very easily know that the return type matches.
     * It's not doing that and I'm not sure why. I have updated the interface to make sure
     * we communicate that this expects a GameCollection, but it feels wrong to do that.
     * It should have allowed the return type to stay as a JsonResponse.
     *
     * @param Request $request
     * @return GameCollection
     */
    public function browse(Request $request): GameCollection;

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
