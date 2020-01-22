<?php

namespace App\Http\Middleware\IDPs;

use Closure;

use App\User;
use App\TempIdp;
use App\Helper\RoleChecker;
use App\Settings;

class ViewTempIDPMiddleware
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

            $idp = TempIdp::withTrashed()->findOrFail($request->id);

            /* Fetch settings */
            $settings = Settings::get()->first();


            /* Check if its the OD */
            if($user->id != $settings->od->id) {

                /* Or the approver */
                if($user->id != $idp->approver->id) {

                    $roleChecker = new RoleChecker();


                    /* Check if user has permission */
                    if(!$roleChecker->isCompanyAdmin('Adding/Editing of Learning Activities', $idp))
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
