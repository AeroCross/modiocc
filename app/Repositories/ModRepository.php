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
}
