<?php

namespace App\Http\Middleware\Requests;

use Closure;

use App\User;

class ExportRequestMiddleware
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
        $user = $request->user();


        /* Check if super admin */
        if(!$user->isSuperUser()) {        

            /* Check if user has permission */
            if(!$user->hasRole('Generating of Admin Reports') && !$user->hasRole('Generating of HR Reports') && !$user->hasRole('Generating of L&D Reports'))
                return $this->redirectRoute();
        }

        return $next($request);
    }

    /**
     * Handle the unauthorized redirect
     *
     * @return redirect
     */
    public function redirectRoute() {
        return redirect()->route('error.unauthorized');
    }
}
