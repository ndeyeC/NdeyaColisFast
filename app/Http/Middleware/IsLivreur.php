<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class IsLivreur
{
    public function handle($request, Closure $next)
    {
        if (Auth::check() && Auth::user()->isLivreur()) {
            return $next($request);
        }

        abort(403, 'Accès refusé - Livreurs uniquement');
    }
}
