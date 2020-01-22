<?php

namespace App\Http\Middleware\Tickets;

use Closure;

use App\User;
use App\Ticket;
use App\Helper\RoleChecker;

class ViewTicketMiddleware
{
    private $user;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $this->user = $request->user();


        /* Check if super admin */
        if(!$this->user->isSuperUser()) {

            $ticket = Ticket::findOrFail($request->id);

            /* Or the owner / assignee */
            if($this->user->id != $ticket->owner->id && $this->user->id != $ticket->form->assignee_id) {

                $companyTech = $ticket->form->getCompanyTechnicians();
                $companyTechIDs = $companyTech ? $companyTech->pluck('id') : collect([]);                

                /* Or the technician */
                if($this->user->id != $ticket->technician_id && !$companyTechIDs->intersect([$this->user->id])->count()) {

                    $roleChecker = new RoleChecker();

                    if (in_array($ticket->id, $this->getIncludedTicketIDs())) {
                        return $next($request);
                    }

                    return $this->redirectRoute($request);
                }
            }
        }

        return $next($request);
    }

    private function getIncludedTicketIDs() {

        /* Set necessary data */  
        $tickets = [];


        foreach ($this->user->groups as $key => $group) {
            
            if($group->hasRole('Updating of Ticket Status') || 
                $group->hasRole('Editing/Removing of Tickets') ||
                $group->hasRole('Generating of Ticketing Reports')) {

                /* If one group has access to all return immediately */
                if($group->type == 0) {
                    return Ticket::select(Ticket::MINIMAL_COLUMNS)
                                    ->pluck('id')
                                    ->toArray();
                }


                /* Fetch tickets */
                $ids = Ticket::join('forms', 'forms.id', '=', 'tickets.form_id')
                                ->join('form_templates', 'form_templates.id', '=', 'forms.form_template_id')
                                ->select(['tickets.id AS id', 'tickets.form_id AS tickets.form_id', 'forms.id AS forms.id', 'forms.form_template_id AS forms.form_template_id', 'form_templates.id AS form_templates.id', 'form_templates.type AS form_templates.type'])
                                ->where('form_templates.type', ($group->type - 1))
                                ->pluck('id')
                                ->toArray();

                                // http_response_code(500); dd($ids);


                array_push($tickets, $ids);
            }
        }

        $tickets = array_flatten($tickets);

        /* Fetch ticket that is assigned to the current user */
        $tickets = array_merge($tickets, $this->user->getTicketApproval()->pluck('id')->toArray());


        return array_unique($tickets);
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
