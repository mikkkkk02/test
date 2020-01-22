<?php

namespace App\Http\Middleware\MrReservations;

use Closure;

use App\Company;

class MrReservationIndexMiddleware
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
            if(!$user->hasRole('Adding/Editing of Meeting Reservations') && !$user->hasRole('Viewing of Meeting Room Reservations')) {
                if (!in_array($user->id, Company::getTechnicianIds())) {
                    return $this->redirectRoute($request);
                }
            }
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
