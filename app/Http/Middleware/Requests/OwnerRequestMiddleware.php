<?php

namespace App\Http\Middleware\Requests;

use Closure;

use App\User;
use App\Form;

class OwnerRequestMiddleware
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
        $form = Form::withTrashed()->findOrFail($request->id);        


        /* Check if super admin */
        if(!$user->isSuperUser()) {

            /* Check if user has permission */
            if($user->id != $form->employee->id && $user->id != $form->assignee_id)
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
