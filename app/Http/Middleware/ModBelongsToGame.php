<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Routing\Exceptions\InvalidSignatureException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ModBelongsToGame extends Middleware
{
    public function handle($request, \Closure $next, ...$guards){
        if (! $mod = $request->route()->parameter('mod')) {
            return $next($request);
            } elseif (! $game = $request->route()->parameter('game')) {
            return $next($request);
        }

        // todo finish the middleware

        return $next($request);
    }
}
