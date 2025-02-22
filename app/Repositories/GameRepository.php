<?php

namespace App\Repositories;

use App\Models\Game;

class GameRepository extends BaseRepository
{
    public function __construct() {}

    public function paginate(int $perPage)
    {
        return Game::paginate($perPage);
    }

    public function create(array $data): ?Game
    {
        return Game::create($data);
    }

    public function find(int $id): ?Game
    {
        return Game::find($id);
    }

    public function update(Game $game, array $data): ?Game
    {
        $game->update($data);
        return $game->refresh();
    }

    public function delete(Game $game): int
    {
        return $game->delete();
    }
}
