<?php

namespace App\Http\Middleware\Events;

use Closure;

use App\User;
use App\Event;
use App\Helper\RoleChecker;

class EditEventParticipantMiddleware
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

            $event = Event::findOrFail($request->id);


            /* Check if user has permission */
            if(!$user->hasRole('Add/Remove Participants to Event'))
                return $this->redirectRoute($request);               
        }

        return $next($request);
    }

    /**
     * Handle the unauthorized redirect
     *
     * @return redirect
     */
    public function redirectRoute($request) {

        /* Check if called via ajax*/
        if($request->ajax())
            return response(['Permission' => 'Unauthorized process'], 422);

        return redirect()->route('error.unauthorized');
    }    
}
