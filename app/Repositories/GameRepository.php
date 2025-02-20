<?php

namespace App\Repositories;

/**
 * GameRepository
 *
 * @todo Fill this class with logic relating to model/record management for games, the repository layer is responsible
 *   for solely dealing with the database
 */
class GameRepository extends BaseRepository
{
    private GameRepository $gameRepo;

    public function __construct(GameRepository $gameRepo)
    {
    }
}
