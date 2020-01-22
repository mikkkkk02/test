<?php

namespace App\Http\Middleware\IDPs;

use Closure;

use App\Idp;
use App\Helper\RoleChecker;

class ImportIDPMiddleware
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

            $roleChecker = new RoleChecker();


            /* Or a company admin */
            if(!$user->hasRole('Batch Uploading of Learning Activities'))
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
            return response()->json([
                'response' => 0,
                'message' => 'Unauthorized access',
                'logs' => ['success' => '', 'errors' => ''],
            ]);


        return redirect()->route('error.unauthorized');
    }   
}
