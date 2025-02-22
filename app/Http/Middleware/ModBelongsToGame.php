<?php

namespace App\Http\Middleware;

use App\Repositories\GameRepository;
use App\Repositories\ModRepository;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class ModBelongsToGame extends Middleware
{
    public function __construct(protected GameRepository $gameRepository, protected ModRepository $modRepository) {}

    public function handle($request, \Closure $next, ...$guards)
    {
        $game = $this->gameRepository->find($request->route()->parameter('gameId'));
        $mod = $this->modRepository->find($request->route()->parameter('modId'));

        if ((empty($game) || empty($mod)) || $game->id != $mod->game->id) {
            return response()->json(['message' => 'Not found.'], 404);
        }

        return $next($request);
    }
}
