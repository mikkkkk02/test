<?php

namespace App\Http\Middleware\IDPs;

use Closure;

use App\User;
use App\Idp;
use App\Helper\RoleChecker;

class DeleteIDPMiddleware
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

            $idp = Idp::findOrFail($request->id);
            $roleChecker = new RoleChecker();


            /* Check if user has permission */
            if(!$roleChecker->isCompanyAdmin('Adding/Editing of Learning Activities', $idp))
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
