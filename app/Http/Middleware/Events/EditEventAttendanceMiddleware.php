<?php

namespace App\Http\Middleware\Events;

use Closure;

use App\User;
use App\EventParticipant;
use App\Helper\RoleChecker;

class EditEventAttendanceMiddleware
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

            $event = EventParticipant::findOrFail($request->id);


            /* Check if user has permission */
            if(!$user->hasRole('Confirm Attendance of Participants'))
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
