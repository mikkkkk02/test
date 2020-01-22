<?php

namespace App\Http\Middleware\FormUpdates;

use Closure;

use App\User;
use App\Form;
use App\TempForm;

class ViewFormUpdateMiddleware
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

            $tempForm = TempForm::withTrashed()->findOrFail($request->id);

            /* Fetch approver IDs */
            $approverIDs = $tempForm->approvers ? $tempForm->approvers->pluck('approver_id') : [];

            /* Check if user is the owner, the supervisor, or the technician */
            if($tempForm->employee->id != $user->id && 
                $tempForm->employee->supervisor_id != $user->id  && 
                !$approverIDs->intersect([$user->id])->count())
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
