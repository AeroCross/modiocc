<?php

namespace App\Services;

use App\Repositories\ModRepository;
use Illuminate\Http\Request;

/**
 * Business logic for Mod management.
 */
class ModService
{
    public function __construct(protected ModRepository $modRepository) {}

    /** Fetches all mods for a given game with pagination.
     *
     * @param Request $request
     * @param integer $perPage
     * @return void
     */
    public function getAllPaginated(Request $request, int $perPage = 10)
    {
        $validatedData = validator($request->route()->parameters(), [
            'gameId' => ['required'],
        ])->validate();

        return $this->modRepository->paginate($validatedData['gameId'], $perPage);
    }

    /** Finds a mod.
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
