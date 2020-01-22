<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests\Events\EventStorePost;
use App\Http\Requests\Events\EventAttendParticipantPost;
use App\Http\Requests\Events\EventCancelParticipantPost;
use App\Http\Requests\Events\EventRemoveParticipantPost;

use App\Notifications\Events\EventWasAdded;

use Carbon\Carbon;

use App\Group;
use App\Event;
use App\EventParticipant;
use App\EventCategory;

use App\FormTemplate;
use App\FormTemplateCategory;
use App\FormTemplateField;
use App\FormTemplateOption;

class EventController extends Controller
{
    /**
     * Instantiate a new EventController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('App\Http\Middleware\Events\ViewEventMiddleware', ['only' => ['update']]);

        $this->middleware('App\Http\Middleware\Events\EditEventParticipantMiddleware', ['only' => ['removeParticipants']]);
    }    

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        /* Fetch events */
        $eventArray = Event::fetchEvents(Event::get());


        return view('pages.events.events', [
            'events' => $eventArray,
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexMyOwn()
    {
        /* Fetch events */
        $user = \Auth::user();
        $eventArray = Event::fetchEvents($user->events()->get());
        // dd($eventArray);

        return view('pages.events.eventmyown', [
            'events' => $eventArray,
        ]);
    } 

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexMyTeam()
    {
        $categories = \Auth::user()->renderSubordinateFilter();


        return view('pages.events.eventmyteam', [
            'categories' => $categories,
        ]);
    }       

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $formTemplates = FormTemplate::select(FormTemplate::MINIMAL_COLUMNS)->where('form_template_category_id', FormTemplateCategory::EVENT)->get();
        $categories = EventCategory::select(EventCategory::MINIMAL_COLUMNS)->get();


        return view('pages.events.createevent', [
            'formTemplates' => $formTemplates,
            'categories' => $categories,
        ]);
    }

    /**
     * Show the form for creating a new resource's form request.
     *
     * @return \Illuminate\Http\Response
     */
    public function createRequest($event, $form)
    {
        $event = Event::findOrFail($event);

        if (!$event->canStillRegister() || $event->isRegistered(\Auth::user()->id)) {
            return redirect()->back();
        }

        $formTemplate = FormTemplate::select(FormTemplate::MINIMAL_COLUMNS)
                                    ->with('fields', 'fields.options')
                                    ->findOrFail($form);

        $types = FormTemplateField::getTypes();
        $tableTypes = FormTemplateOption::getTypes();


        return view('pages.requests.createeventrequest', [
            'event' => $event,
            'formTemplate' => $formTemplate,
            'types' => $types,
            'tableTypes' => $tableTypes,
        ]);                                    
    }    

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(EventStorePost $request)
    {
        $user = \Auth::user();
        $vars = $request->except(['times', 'start_time', 'end_time',
                                'hour', 'minute', 'meridian']);
        $vars['creator_id'] = $user->id;
        $vars['updater_id'] = $user->id;


        /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();


        /* Create the event */
        $event = Event::create($vars);


        /* Set times */
        $event->updateTime($request->input('isSameTime'), $request->input('times'));
        
        /* Notify the admin */
        $admins = Group::where('name', 'Super User')->get()->first();
        foreach ($admins->users as $admin) {
            $admin->notify(new EventWasAdded($event, $user));
        }


        /*
        | @End Transaction
        |---------------------------------------------*/
        \DB::commit();


        return response()->json([
            'response' => 1,
            'redirectURL' => route('event.show', $event->id),
            'title' => 'Create event',
            'message' => 'Successfully created event ' . $event->name
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $event = Event::findOrFail($id);

        $status = EventParticipant::getStatus();
        $attendance = EventParticipant::getAttendance();

        $formTemplates = FormTemplate::select(FormTemplate::MINIMAL_COLUMNS)->where('form_template_category_id', FormTemplateCategory::EVENT)->get();
        $categories = EventCategory::select(EventCategory::MINIMAL_COLUMNS)->get(); 


        return view('pages.events.showevent', [
            'event' => $event,
            'status' => $status,
            'attendance' => $attendance,
            'formTemplates' => $formTemplates,
            'categories' => $categories,
        ]);
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
    public function update(EventStorePost $request, $id)
    {
        $event = Event::findOrFail($id);
        $vars = $request->except(['times', 'start_time', 'end_time',
                                'hour', 'minute', 'meridian']);
        $vars['hasWeekend'] = $request->has('hasWeekend') ? true : false;
        $vars['updater_id'] = \Auth::user()->id;


        /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();


        /* Check if limit has been changed */
        if($event->limit != $request->input('limit'))
            $event->updateParticipants($request->limit);

        /* Update the event */
        $event->update($vars);

        /* Update times */
        $event->updateTime($request->input('isSameTime'), $request->input('times'));


        /*
        | @End Transaction
        |---------------------------------------------*/
        \DB::commit();


        return response()->json([
            'response' => 1,
            'title' => 'Update event',
            'message' => 'Successfully updated event ' . $event->name
        ]);
    }

    /**
     * Cancel the specified resource's Participant in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function cancel(EventCancelParticipantPost $request, $id)
    {
        $event = Event::findOrFail($id);
        $participant = User::withTrashed()->findOrFail($request->input('participant_id'));


        /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();


        /* Remove the event participant */
        $event->cancel($participant->id, $request->input('reason'));


        /*
        | @End Transaction
        |---------------------------------------------*/
        \DB::commit();


        return response()->json([
            'response' => 1,
            'message' => 'Successfully removed ' . $participant->renderFullname() . ' from event'
        ]);
    }

    /**
     * Remove the specified resource's Participant in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function removeParticipants(EventRemoveParticipantPost $request, $id)
    {
        $event = Event::findOrFail($id);
        $participants = $request->input('participants');


        /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();


        /* Remove the event participant */
        $event->removeParticipants($participants);


        /*
        | @End Transaction
        |---------------------------------------------*/
        \DB::commit();


        return response()->json([
            'response' => 1,            
            'redirectURL' => route('event.show', $event->id),
            'title' => 'Remove participants',
            'message' => 'Successfully removed ' . count($participants) . ' employee(s) from event ' . $event->title
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