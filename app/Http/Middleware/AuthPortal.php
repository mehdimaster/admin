<?php

namespace App\Http\Middleware;

use Request;
use Redirect;
use App;
use Cookie;
use Closure;
use Session;
use Input;

class AuthPortal
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

        $lang = App::getLocale();
        if($lang == null){
            $lang = 'fa';
        }
        if($request->user() == null){
            return Redirect::to(url("$lang/portal/login"))->withInput(Input::all())->with('login', 'false');
        }

        if(isset($request->user()->user_status) && $request->user()->user_status != 2 ){
            return Redirect::to(url("$lang/portal/login"))->withInput(Input::all())->with('login', 'false');
        }

        return $next($request);
    }

}
