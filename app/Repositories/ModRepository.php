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
        $mods = Mod::where('game_id', $gameId);

        if ($mods->count() == 0) {
            return false;
        }

        return $mods->paginate($perPage);
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

    public function update(Mod $mod, array $data): ?Mod
    {
        $mod->update($data);
        return $mod->refresh();
    }
}
