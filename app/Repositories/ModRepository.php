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

    public function create(array $data): ?Mod
    {
        return Mod::create($data);
    }

    public function exists(string $name, int $gameId = null)
    {
        return Mod::where('name', $name)->where('game_id', $gameId)->get()->isNotEmpty();
    }

    public function delete(Mod $mod): int
    {
        return $mod->delete();
    }
}
