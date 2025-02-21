<?php

namespace App\Repositories;

use App\Models\Game;

/**
 * GameRepository
 *
 * @todo Fill this class with logic relating to model/record management for games, the repository layer is responsible
 *   for solely dealing with the database
 */
class GameRepository extends BaseRepository
{
    private GameRepository $gameRepo;

    public function __construct() {}

    public function paginate(int $perPage)
    {
        return Game::paginate($perPage);
    }

    public function create(array $data): ?Game
    {
        return Game::create($data);
    }
}
