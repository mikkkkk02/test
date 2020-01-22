<?php

namespace App\Http\Middleware\Requests;

use Closure;

use App\User;
use App\Form;

class ViewRequestMiddleware
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
        if(!$user->isSuperUser() && !$user->hasRole('Creating/Designing/Editing/Removing of Forms')) {

            $form = Form::withTrashed()->findOrFail($request->id);

            if ($user->id !== $form->assignee_id) {

                /* Fetch approver IDs */
                $approverIDs = $form->approvers ? $form->approvers->pluck('approver_id') : [];
                /* Fetch technician ID */
                $technicianID = $form->ticket && $form->ticket->technician ? $form->ticket->technician->id : null;

                /* Fetch company technicians */
                $companyTechIDs = $form->getCompanyTechnicians();
                $companyTechIDs = $companyTechIDs ? $companyTechIDs->pluck('id') : [];


                /* Check if user is the owner, the supervisor, or the technician */
                if($form->employee->id != $user->id && 
                    $form->employee->supervisor_id != $user->id  && 
                    !$approverIDs->intersect([$user->id])->count() && 
                    !$companyTechIDs->intersect([$user->id])->count() && 
                    $technicianID != $user->id)
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
