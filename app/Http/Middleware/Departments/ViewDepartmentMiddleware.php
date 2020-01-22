<?php

namespace App\Http\Middleware\Departments;

use Closure;

use App\User;
use App\Department;
use App\Helper\RoleChecker;

class ViewDepartmentMiddleware
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

            $department = Department::findOrFail($request->id);
            $roleChecker = new RoleChecker();


            /* Check if user has permission */
            if(!$roleChecker->isCompanyAdmin('Adding/Editing of Department', $department))
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
