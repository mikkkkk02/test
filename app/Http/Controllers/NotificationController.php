<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('pages.notifications.notifications');
    }

    /**
     * Mark all notification as read.
     *
     * @return \Illuminate\Http\Response
     */
    public function markAsRead()
    {
    	$user = \Auth::user();


    	/* Mark as Read all unread notifications */
    	$user->unreadNotifications->markAsRead();

    	return response()->json([
    		'response' => 1,
    	]);
    }    
}
