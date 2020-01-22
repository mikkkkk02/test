<?php

namespace App\Http\Middleware;

use Closure;

use App\Developer;

class DeveloperMiddleware
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
        /* Fetch dev key */
        $secret = env('DEV_SECRET_KEY');
        $key = env('DEV_KEY');

        /* Check if env is on debug & local */
        if(!env('APP_DEBUG') || !\App::environment('Local'))
            return redirect('404 error');

        /* Check & validate dev key */
        if(!$key || !$secret || !$this->validateKey($secret, $key))
            return redirect('404 error');


        return $next($request);
    }

    protected function validateKey($secret, $key) 
    {
        /* Fetch secret key */
        $real = Developer::where('secret', $secret)->get()->first();

        /* Check key */
        if($real && hash_equals($real->key, $key))
            return true;

        return false;
    }
}
