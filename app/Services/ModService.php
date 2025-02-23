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

    /** Create a Mod entry for a given game.
     *
     * @param Request $request
     * @return void
     */
    public function create(Request $request)
    {
        $user = $request->user();
        $inputData =  array_merge($request->input(), $request->route()->parameters());

        $validatedData = validator($inputData, [
            'name' => ['required'],
            'gameId' => ['required', 'exists:\App\Models\Game,id']
        ])->validate();

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

    /** Hard deletes an instance of a Mod.
     *
     * @param integer $id
     * @param Request $request
     * @return void
     */
    public function delete(int $id, Request $request) // TODO: the request should be passed first always, there seem to be an inconsistency here
    {
        $user = $request->user();
        $mod = $this->modRepository->find($id);

        // Not found
        if ($mod === null) {
            return null;
        }

        // Only the owner of the mod, or the owner of the game that the mod is for, can delete a mod
        if ($user->cannot('delete', $mod)) {
            return false;
        }

        return $this->modRepository->delete($mod);
    }

    /** Update an instance of a mod.
     *
     * @param integer $id
     * @param Request $request
     * @return void
     */
    public function update(Request $request, string $id)
    {
        $user = $request->user();
        $mod = $this->modRepository->find($id);

        // Not found
        if ($mod === null) {
            return null;
        }

        // Only the owner can update the game
        if ($user->cannot('update', $mod)) {
            return false;
        }

        // If we want to update with the same name, noop
        if ($mod->name === $request->input('name')) {
            return $mod;
        }

        $validatedData = $request->validate([
            'name' => ['required']
        ]);

        return $this->modRepository->update(mod: $mod, data: [
            'name' => $validatedData['name']
        ]);
    }
}
