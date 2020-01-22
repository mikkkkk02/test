<?php

namespace App\Http\Middleware\Tickets;

use Closure;

use App\User;
use App\Ticket;
use App\Helper\RoleChecker;

class EditTicketMiddleware
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

            $ticket = Ticket::findOrFail($request->id);

            $companyTech = $ticket->form->getCompanyTechnicians();
            $companyTechIDs = $companyTech ? $companyTech->pluck('id') : collect([]);

            /* Or the technician */
            if($user->id != $ticket->technician_id && !$companyTechIDs->intersect([$user->id])->count()) {        

                $roleChecker = new RoleChecker();


                /* Check if user has permission */
                if($roleChecker->isCompanyAdmin('Editing/Removing of Tickets', $ticket) || $roleChecker->isCompanyAdmin('Updating of Ticket Status', $ticket))
                    return $next($request);     


                return $this->redirectRoute($request);
            }
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
