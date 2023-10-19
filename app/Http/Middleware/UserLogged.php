<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;

class UserLogged
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $path = Route::getCurrentRoute()->uri();

        if (!Auth::check()) {
            if ($path == '/') {
                return Redirect::to('login');
            }
            return Redirect::to('login')->with('error', 'Você precisa estar logado para acessar essa página!');
        }



        return $next($request);
    }
}
