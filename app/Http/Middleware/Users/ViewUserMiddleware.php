<?php

namespace App\Http\Middleware\Users;

use Closure;

use App\User;
use App\Helper\RoleChecker;

class ViewUserMiddleware
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


            /* Or a company admin */
            if(!$roleChecker->isCompanyAdmin('Adding/Editing of User Responsibilities/Groups', $employee))
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
