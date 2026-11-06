<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class authentication
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
        // Remember me: the session expired but the remember cookie logged the user back in
        if (!session()->get('user_login') && Auth::check()) {
            $u = Auth::user();
            session()->put('nice_name', $u->user_nicename);
            session()->put('user_id', $u->ID);
            session()->put('user_email', $u->user_email);
            session()->put('user_login', true);
        }

        if(session()->get('user_login'))
        {
            return $next($request);
         //dd('reach');
        }
        else
        {
            return redirect('/');
           // return redirect()->intended();
        
        }
    }
}
