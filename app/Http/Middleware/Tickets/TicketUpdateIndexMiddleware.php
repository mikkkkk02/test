<?php

namespace App\Http\Middleware\Tickets;

use Closure;

use App\User;
use App\Ticket;
use App\TicketUpdate;
use App\TempTicketUpdate;
use App\Helper\RoleChecker;

class TicketUpdateIndexMiddleware
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

            $tmpTicketUpdate = TempTicketUpdate::findOrFail($request->id);
            
            if(!$tmpTicketUpdate->canApprove($user))
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
