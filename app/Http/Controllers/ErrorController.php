<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ErrorController extends Controller
{
    /**
     * Display the logout page
     *
     * @return \Illuminate\Http\Response
     */
    public function logout()
    {
        return view('errors.logout');
    }

    /**
     * Display the forbidden page
     *
     * @return \Illuminate\Http\Response
     */
    public function forbidden()
    {
        return view('errors.forbidden');
    }

    /**
     * Display the unauthorized page
     *
     * @return \Illuminate\Http\Response
     */
    public function unauthorized()
    {
        return view('errors.unauthorized');
    }    
}
