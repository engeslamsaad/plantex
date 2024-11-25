<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Authenticated
{
    
    public function handle(Request $request, Closure $next)
    {
        
        if(session()->has('admin_id')&&session()->has('loged_user')){

            return $next($request);
        }
        return redirect('login');

    }
}
