<?php

namespace App\Http\Middleware\Rooms;

use Closure;

use App\User;

class RoomIndexMiddleware
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
            if(!$user->hasRole('Adding/Editing of Meeting Rooms'))
                return $this->redirectRoute($request);            
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
