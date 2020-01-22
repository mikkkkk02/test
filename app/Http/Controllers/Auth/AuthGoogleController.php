<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;

use Mail;

use Auth;
use Socialite;

use App\User;
use App\Address;

class AuthGoogleController extends Controller
{
    public function redirectToProvider() {

        /* Keep alive the session redirect URL */
        if(\Session::has('redirect'))
            \Session::keep('redirect');

    	return Socialite::driver('google')->redirect();
    }

    public function handleProviderCallback(Request $request) {

        /* Check if social media login has been authorized */
        if (!$request->has('code') || $request->has('denied')) {
            $this->redirectBack();
        }

        try {

            /* Fetch the google user account */
            $user = Socialite::driver('google')
                    ->stateless()
                    ->user();

    	} catch (Exception $e) {
            $this->redirectBack();
    	}


        /* Check if registered user */
        if(!$this->checkUser($user))
            return redirect()->route('error.forbidden')->with('email', $user->email);

        /* Check session redirect URL */
        $redirect = redirect()->route('dashboard');
        if(\Session::has('redirect'))
            $redirect = redirect(\Session::get('redirect'));

        return $redirect;
    }

    private function checkUser($user)
    {
        $authUser = User::where('email', $user->email)->get()->first();


        /* Check if user exists */
    	if(!$authUser)
            return false;


        /* Check if same google id */
        if($authUser->google_id != $user->id) {
            
            /* Update google credentials */
            $authUser->google_id = $user->id;
            $authUser->google_name = $user->name;
            $authUser->save();
        }

        /* Login the current user */
        Auth::login($authUser, true);


        return true;
    }

    public function redirectBack() {
        return redirect()->back();
    }
}
