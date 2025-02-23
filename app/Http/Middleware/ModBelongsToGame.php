<?php

namespace App\Http\Middleware;

use App\Repositories\GameRepository;
use App\Repositories\ModRepository;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class ModBelongsToGame extends Middleware
{
    public function __construct(protected GameRepository $gameRepository, protected ModRepository $modRepository) {}

    public function handle($request, \Closure $next, ...$guards)
    {
        $gameId = $request->route()->parameter('gameId') ?? $request->input('gameId');
        $modId = $request->route()->parameter('modId') ?? $request->input('modId');

        if ((empty($gameId) || empty($modId))) {
            return response()->json(['message' => 'gameId and modId required'], 400);
        }

        $game = $this->gameRepository->find($gameId);
        $mod = $this->modRepository->find($modId);

        if (($game->id != $mod->game->id)) {
            return response()->json(['message' => 'Not found.'], 404);
        }

        return $next($request);
    }
}
