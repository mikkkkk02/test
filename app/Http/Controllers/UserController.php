<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests\Users\UserStorePost;
use App\Http\Requests\ImportPost;

use Excel;

use App\User;

class UserController extends Controller
{
    /**
     * Instantiate a new UserController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('App\Http\Middleware\Users\ViewUserMiddleware', ['only' => ['update']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserStorePost $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UserStorePost $request, $id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $vars = $request->except([
                    'email', 'email_confirmation',
                    'password', 'newpassword', 'newpassword_confirmation', 
                    'groups',
                ]);


        /* Check if email exist */
        if($request->has('email'))
            $vars['email'] = $request->input('email');


        /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();


        /* Check if change password is toggled */
        // if($request->input('changePassword')) {

        //     /* Check if passwords match */
        //     if(Hash::check($request->input('password'), $user->password)) {

        //         $vars['password'] = Hash::make($request->input('newpassword'));

        //     } else {
        //         return response()->json(['response' => 2, 'errors' => ['password' => array('Old password is incorrect')]]);
        //     }
        // }


        /* Update the user */
        $user->update($vars);

        /* Update the user groups */
        $user->updateGroups($request->input('groups'));        


        /*
        | @End Transaction
        |---------------------------------------------*/
        \DB::commit();


        return response()->json([
            'response' => 1,
            'title' => 'Update employee security & permission',
            'message' => 'Successfully updated employee ' . $user->name
        ]);        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
