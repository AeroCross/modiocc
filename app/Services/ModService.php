<?php

namespace App\Services;

use App\Repositories\ModRepository;
use Illuminate\Http\Request;

/**
 * Business logic for Mod management.
 */
class ModService
{
    private array $upsertValidationRules = [
        'name' => ['required'],
        'gameId' => ['required', 'exists:\App\Models\Game,id']
    ];

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

    /** Create a Mod entry for a given game.
     *
     * @param Request $request
     * @return void
     */
    public function create(Request $request)
    {
        $user = $request->user();
        $inputData =  array_merge($request->input(), $request->route()->parameters());

        $validatedData = validator($inputData, $this->upsertValidationRules)->validate();

        if ($this->modRepository->exists($validatedData['name'], $validatedData['gameId'])) {
            return false;
        }

        // TODO: There's probably a better way to go about this without having to cast query params to int.
        return $this->modRepository->create([
            'user_id' => $user->id,
            'game_id' => (int)$validatedData['gameId'],
            'name' => $validatedData['name']
        ]);
    }
}
