<?php

namespace App\Services;

use App\Repositories\ModRepository;
use Illuminate\Http\Request;

/**
 * Business logic for Mod management.
 *
 * @param GameRepository Optional. An instance of a ModRepository. Creates a new instance on construction.
 */
class ModService
{
    public function __construct(protected ModRepository $modRepository) {}

    /** Finds a Mod.
     *
     * @param Request $request
     * @return void
     */
    public function find(Request $request)
    {
        $validatedData = validator($request->route()->parameters(), [
            'gameId' => ['required'],
            'modId' => ['required'],
        ])->validate();

        return $this->modRepository->find($validatedData['modId']);
    }
}
