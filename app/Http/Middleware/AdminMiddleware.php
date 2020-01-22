<?php

namespace App\Http\Middleware;

use Closure;

class AdminMiddleware
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
        if (\Auth::check()) {
            if ($request->user()->hasRole('Administrator')) {
                
                return $next($request);
            }

            return redirect('404 error');

        } else {
            return redirect('/');
        }
    }
}
