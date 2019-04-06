<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class AdminPortalLoginCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $lang = App::getLocale();
        if($lang == null){
            $lang = 'fa';
        }

        if (!Auth::guard($guard)->check()) {
            return redirect(url("$lang"));
        }

        return $next($request);
    }
}
