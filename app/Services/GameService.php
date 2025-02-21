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

        // Only allow the owner to update their own games
        if ($game->user_id != $user->id) {
            return false;
        }

        // If we want to update with the same name, noop
        if ($game->name === $request->input('name')) {
            return $game;
        }

        $validatedData = $request->validate($this->upsertValidationRules);

        // If we decide that we want to allow to update more fields, we can change this.
        // Otherwise, we make sure that the only thing we can update is the name.

        // TODO: since we already have the model fetched, can we use it instead?
        // This would potentially require the gameRepository to return an instance of itself to chain `->update()`
        return $this->gameRepository->update($id, [
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

        // Only allow the owner to delete their own games
        if ($game->user_id != $user->id) {
            return false;
        }

        return $this->gameRepository->delete($id);
    }
}
