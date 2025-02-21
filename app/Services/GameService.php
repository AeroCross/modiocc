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
        $validatedData = $request->validate([
            'name' => ['required', 'unique:App\Models\Game,name'],
        ]);

        return $this->gameRepository->create([
            'user_id' => $user->id,
            'name' => $validatedData['name']
        ]);
    }
}
