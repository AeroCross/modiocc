<?php

namespace App\Repositories;

/**
 * GameRepository
 *
 * @todo Fill this class with logic relating to model/record management for mods, the repository layer is responsible
 *   for solely dealing with the database
 */
class ModRepository extends BaseRepository
{
    private ModRepository $modRepo;

    public function __construct(ModRepository $modRepo)
    {
    }
}
