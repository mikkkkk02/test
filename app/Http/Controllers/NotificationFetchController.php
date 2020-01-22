<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User;

class NotificationFetchController extends Controller
{
    /**
     * Fetch the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function fetch(Request $request)
    {
        /* Get default variable */
        $user = \Auth::user();


        /* Query */
        $notifications = $user->notifications();

        /* Do the pagination */
        $pagination = $notifications ? $notifications->paginate(10) : array_merge($notifications, ['data' => []]);

        return response()->json([
            'lists' => $pagination,
        ]);
    }  
}
