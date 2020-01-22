<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Carbon\Carbon;

use App\Event;
use App\Form;
use App\FormTemplate;
use App\MrReservationTime;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        /* Fetch events */
        $eventArray = Event::fetchEvents(Event::get());


        /* Fetch meeting room */
        // !@REFACTOR
        $formTemplateIDs = FormTemplate::select('id', 'request_type')
                                        ->where('request_type', FormTemplate::MEETINGROOM)
                                        ->pluck('id');

        $forms = Form::select('id', 'form_template_id', 'mr_title', 'mr_date', 'mr_start_time', 'mr_end_time')
                    ->whereIn('form_template_id', $formTemplateIDs)
                    ->where('status', Form::APPROVED)
                    ->get();

        $meetinRoomEvents = MrReservationTime::toCalendarObject(MrReservationTime::get());


        return view('pages.dashboard', [
            'events' => $eventArray,
            'meetinRoomEvents' => $meetinRoomEvents,
        ]);
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
        //
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
