<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Mod;

class ModPolicy
{
    public function __construct() {}

    public function update(User $user, Mod $mod)
    {
        // A User can only update a mod if they own the game, or they own the mod.
        return ($user->id === $mod->user_id) || ($user->id === $mod->game->user_id);
    }

    public function delete(User $user, Mod $mod)
    {
        // A User can only delete a mod if they own the game, or they own the mod.
        return ($user->id === $mod->user_id) || ($user->id === $mod->game->user_id);
    }
}
