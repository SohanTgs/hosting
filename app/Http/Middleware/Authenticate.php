<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Closure;
use Auth;
use Illuminate\Support\Facades\Session;

class Authenticate 
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */

    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            return $next($request);
        }

        if( Session::has('previous') ){ 
            session()->put('after_login_route', Session::get('previous'));
            Session::flash('after_login', true);
        }
        
        return redirect()->route('user.login');
    }



}
