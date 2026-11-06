<?php

namespace App\Http\Middleware;

use Closure;

class AdminAuthentication
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
        if(session()->get('admin_id'))
        {
            return $next($request);
         //dd('reach');
        }
        else
        {
            session()->flash('message','Please Login First.');
            return redirect('adminlogin');
           // return redirect()->intended();
        
        }
    }
}
