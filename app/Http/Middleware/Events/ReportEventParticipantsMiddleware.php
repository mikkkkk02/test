<?php

namespace App\Http\Middleware\Events;

use Closure;

use App\User;

class ReportEventParticipantsMiddleware
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
            if(!$user->hasRole('Generating of BBLS Reports'))
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
