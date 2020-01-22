<?php

namespace App\Http\Middleware\Requests;

use Closure;

use App\User;
use App\Form;

class EditRequestUpdateMiddleware
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
        $form = Form::withTrashed()->findOrFail($request->id);


        /* Check if user can do updates */
        if(!$form->canUpdate())
            return $this->redirectRoute($request);

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
