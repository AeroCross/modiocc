<?php

namespace App\Services;

use App\Repositories\GameRepository;
use Illuminate\Http\Request;

/**
 * Business logic for Game management.
 *
 * @param GameRepository Optional. An instance of a GameRepository. Creates a new instance on construction.
 */
class GameService
{
    private GameRepository $gameRepository;
    private array $upsertValidationRules = ['name' => ['required', 'unique:App\Models\Game,name']];

    public function __construct(GameRepository $gameRepository = null)
    {
        $this->gameRepository = $gameRepository ?? new GameRepository;
    }

    /** Fetches all games with pagination.
     *
     * @param integer $perPage
     * @return void
     */
    public function getAllPaginated(int $perPage = 10)
    {
        return $this->gameRepository->paginate($perPage);
    }

    /** Create a Game entry.
     *
     * @param Request $request
     * @return void
     */
    public function create(Request $request)
    {
        $user = $request->user();
        $validatedData = $request->validate($this->upsertValidationRules);

        return $this->gameRepository->create([
            'user_id' => $user->id,
            'name' => $validatedData['name']
        ]);
    }

    /** Finds a Game.
     *
     * @param Request $request
     * @return void
     */
    public function find(Request $request)
    {
        $validatedData = validator($request->route()->parameters(), [
            'gameId' => ['required'],
        ])->validate();

        return $this->gameRepository->find($validatedData['gameId']);
    }

    /** Update an instance of a game.
     *
     * @param integer $id
     * @param Request $request
     * @return void
     */
    public function update(int $id, Request $request)
    {
        $user = $request->user();
        $game = $this->gameRepository->find($id);

        // Not found
        if ($game === null) {
            return null;
        }

        // Only the owner can update the game
        if ($user->cannot('update', $game)) {
            return false;
        }

        // If we want to update with the same name, noop
        if ($game->name === $request->input('name')) {
            return $game;
        }

        $validatedData = $request->validate($this->upsertValidationRules);

        return $this->gameRepository->update(game: $game, data: [
            'name' => $validatedData['name']
        ]);
    }

    /** Hard deletes an instance of a Game.
     *
     * @param integer $id
     * @param Request $request
     * @return void
     */
    public function delete(int $id, Request $request)
    {
        $user = $request->user();
        $game = $this->gameRepository->find($id);

        // Not found
        if ($game === null) {
            return null;
        }

        // Only the owner can delete their own games
        if ($user->cannot('delete', $game)) {
            return false;
        }

        return $this->gameRepository->delete($game);
    }
}
