<?php

namespace App\Repositories;

use App\Models\Mod;

class ModRepository extends BaseRepository
{
    public function __construct() {}

    public function find(int $id): ?Mod
    {
        return Mod::find($id);
    }

    public function paginate(string $gameId, int $perPage)
    {
        return Mod::where('game_id', $gameId)->paginate($perPage);
    }
}
