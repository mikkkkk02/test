<?php

namespace App\Http\Middleware\Employees;

use Closure;

use App\User;
use App\Helper\RoleChecker;

class ViewEmployeeMiddleware
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

            $employee = User::withTrashed()->findOrFail($request->id);
            $roleChecker = new RoleChecker();


            /* Check if its the current user */
            if($user->id == $request->id) {

                /* Check if user has permission */
                if(!$user->hasRole('Updating of Profile'))
                    return $this->redirectRoute($request);

            } else {

                /* Or the supervisor */
                if($user->id != $employee->supervisor_id) {
                    /* Or a company admin */
                    if(!$roleChecker->isCompanyAdmin('Adding/Editing of Employee Profile', $employee))
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
    public function redirectRoute($request) {

        /* Check if called via ajax*/
        if($request->ajax())
            return response(['Permission' => 'Unauthorized process'], 422);

        return redirect()->route('error.unauthorized');
    }    
}
