<?php

namespace App\Services;

use App\Repositories\GameRepository;
use Illuminate\Http\Request;

/**
 * GameService
 *
 * @todo Fill this class with business logic relating to games, the service layer is responsible for solving
 *   the problems and producing the result.
 */
class GameService
{
    private GameRepository $gameRepository;
    private array $upsertValidationRules = ['name' => ['required', 'unique:App\Models\Game,name']];

    public function __construct(GameRepository $gameRepository = null)
    {
        $this->gameRepository = $gameRepository ?? new GameRepository;
    }

    public function getAllPaginated(int $perPage = 10)
    {
        return $this->gameRepository->paginate($perPage);
    }

    public function create(Request $request)
    {
        $user = $request->user();
        $validatedData = $request->validate($this->upsertValidationRules);

        return $this->gameRepository->create([
            'user_id' => $user->id,
            'name' => $validatedData['name']
        ]);
    }

    public function find(Request $request)
    {
        validator($request->route()->parameters(), [
            'id' => ['required'],
        ])->validate();

        return $this->gameRepository->find($request->id);
    }

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
