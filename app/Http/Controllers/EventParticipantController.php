<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests\Events\EventAttendanceUpdatePost;
use App\Http\Requests\Events\EventApproveAttendancePost;
use App\Http\Requests\Events\EventDisapproveAttendancePost;

use App\Notifications\Events\EventParticipantWasAdded;
use App\Notifications\Events\EventParticipantWasRemoved;
use App\Notifications\Events\EventParticipantWasInQueue;
use App\Notifications\Events\EventParticipantWasDisapproved;

use App\EventParticipant;

class EventParticipantController extends Controller
{
    /**
     * Instantiate a new EventController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('App\Http\Middleware\Events\EditEventAttendanceMiddleware', ['only' => ['attended', 'noshow']]);
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
    public function store(Request $request)
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
    public function update(Request $request, $id)
    {
        
    }

    /**
     * Update the specified resource's attendance in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function attended(EventAttendanceUpdatePost $request, $id)
    {
        $eventParticipant = EventParticipant::findOrFail($id);


        /* Set the event as attended */
        $eventParticipant->setAsAttended(true);

        return response()->json([
            'response' => 1,
            'attendance' => $eventParticipant->hasAttended,
            'message' => $eventParticipant->renderAttendance(),
        ]);
    }

    /**
     * Update the specified resource's attendance in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function noshow(EventAttendanceUpdatePost $request, $id)
    {
        $eventParticipant = EventParticipant::findOrFail($id);


        /* Set the event as no show */
        $eventParticipant->setAsNoShow(true);

        return response()->json([
            'response' => 1,
            'attendance' => $eventParticipant->hasAttended,
            'message' => $eventParticipant->renderAttendance(),
        ]);
    }    

    /**
     * Approve the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function approve(EventApproveAttendancePost $request, $id)
    {
        $eventParticipant = EventParticipant::findOrFail($id);


        /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();


        /* Approve the event participant */
        if($eventParticipant->approve()) {

            $event = $eventParticipant->event;
            $user = $eventParticipant->participant;


            /* Check participant limit */
            if($event->addAttending()) {
                

                /* Notify the participant & admin */
                $user->notify(new EventParticipantWasAdded($event, $user, true));
                $user->supervisor->notify(new EventParticipantWasAdded($event, $user, false)); 

            } else {
                
                /* Notify for user */
                $user->notify(new EventParticipantWasInQueue($event, $user, true));
                $user->supervisor->notify(new EventParticipantWasInQueue($event, $user, false));
            }


            /*
            | @End Transaction
            |---------------------------------------------*/
            \DB::commit();


            return response()->json([
                'response' => 1,
                'status' => $eventParticipant->status,
                'message' => $eventParticipant->renderStatus(),
            ]);
        }        


        return response()->json([
            'response' => 0,
            'message' => 'There seems to be a problem approving the request'
        ]);
    }

    /**
     * Disapprove the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function disapprove(EventDisapproveAttendancePost $request, $id)
    {
        $eventParticipant = EventParticipant::findOrFail($id);


        /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();


        /* Disapprove the event participant */
        if($eventParticipant->disapprove($request->input('reason'))) {

            $event = $eventParticipant->event;
            $user = $eventParticipant->participant;

            
            /* Notify the user */
            $user->notify(new EventParticipantWasDisapproved($event, $user, $request->input('reason')));


            /*
            | @End Transaction
            |---------------------------------------------*/
            \DB::commit();


            return response()->json([
                'response' => 1,
                'status' => $eventParticipant->status,
                'message' => $eventParticipant->renderStatus(),
            ]);
        }

        return response()->json([
            'response' => 0,
            'message' => 'There seems to be a problem disapproving the request'
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
