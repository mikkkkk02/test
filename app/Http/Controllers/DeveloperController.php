<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User;

class DeveloperController extends Controller
{
	public function switchAccount(Request $request) 
	{
		$user = User::findOrFail($request->input('user_id'));

		/* Login the current user */
        \Auth::login($user, true);


        return response()->json([
            'response' => 1,
            'redirectURL' => route('dashboard'),
        ]);        
	}
}
