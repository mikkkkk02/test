<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Notifications\Events\EventParticipantWasAdded;
use App\Notifications\Events\EventParticipantWasRemoved;
use App\Notifications\Events\EventWillAttend;

use Laravel\Scout\Searchable;
use Carbon\Carbon;

use App\Helper\Helpers;

use App\EventTime;
use App\EventParticipant;

class Event extends Model
{   
    use Searchable;

    public $asYouType = true;
        
    /*
    |-----------------------------------------------
    | @Columns
    |-----------------------------------------------
    */
    const MINIMAL_COLUMNS = [
        'id', 'form_template_id', 'event_category_id',
        'title', 'facilitator', 'venue', 'hours', 
        'limit', 'attending', 'isSameTime', 'hasWeekend',
        'start_date', 'end_date',
        'start_time', 'end_time',
    ];

    const TABLE_COLUMNS = [
        'id', 'form_template_id', 'event_category_id',
        'title', 'facilitator', 'venue', 'hours', 'description',
        'limit', 'attending', 'isSameTime', 'hasWeekend',
        'start_date', 'end_date',
        'start_time', 'end_time',
    ];    

    protected $guarded = [];
    protected $dates = ['registration_date', 'start_date', 'end_date', 'start_time', 'end_time'];
    protected $appends = ['extra'];


    public function form_template() {
    	return $this->belongsTo(FormTemplate::class)->withTrashed();
    }

    public function category() {
    	return $this->belongsTo(EventCategory::class);
    }

    public function times() {
        return $this->hasMany(EventTime::class);
    }

    public function participants() {
    	return $this->hasMany(EventParticipant::class, 'event_id');
    }

    public function creator() {
        return $this->belongsTo(User::class, 'creator_id')->withTrashed();
    }

    public function updater() {
        return $this->belongsTo(User::class, 'updater_id')->withTrashed();
    }


    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        $searchable = [
            'id' => $this->id,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'time' => $this->renderTime(),
            'hours' => $this->hours,
            'title' => $this->title,
            'facilitator' => $this->facilitator,
            'venue' => $this->venue,
            'description' => $this->description,
        ];

        return $searchable;
    }


    /*
    |-----------------------------------------------
    | @Helper
    |-----------------------------------------------
    */
    public function getExtraAttribute() {
        return $this->attributes['extra'] = [
            'participants' => $this->renderParticipants(),
            'time' => $this->renderTime(),
            'view' => $this->renderViewURL(),
        ];
    }

    public function getAttendingParticipants() {

        $participants = $this->participants()
                    ->where('status', EventParticipant::APPROVED)
                    ->orderBy('approved_at', 'asc');


        if($this->limit > 0)
            return $participants->take($this->limit);

        return $participants;
    }

    public function setTime($time) {

        /* Create time */
        EventTime::create([
            'event_id' => $this->id,

            'start_time' => Carbon::parse($time['start_time']),
            'end_time' => Carbon::parse($time['end_time']),
        ]);
    }


    /*
    |-----------------------------------------------    
    | @Methods
    |-----------------------------------------------
    */
    public function updateParticipants($newLimit = null, $existing = null) {

        /* Check if there is actually a limit */
        if($this->limit == 0)
            return true;


        /* Fetch old participant list */
        if(!$existing)
            $existing = $this->getAttendingParticipants()->pluck('participant_id')->toArray();

        /* Update limit temporary */
        if($newLimit)
            $this->limit = $newLimit;

        /* Fetch new participant list */
        $current = $this->getAttendingParticipants()->pluck('participant_id')->toArray();


        /*
        | Check for any removed participants
        |-----------------------------------------------*/
        $removedParticipants = array_diff($existing, $current);
        $this->notifyEmployee($removedParticipants, 0);

        /*
        | Check for any added participants
        |-----------------------------------------------*/
        $newParticipants = array_diff($current, $existing);
        $this->notifyEmployee($newParticipants, 1);
    }

    public function notifyEmployee($participants, $boolean) {
        foreach ($participants as $key => $participant) {

            $user = User::findOrFail($participant);            


            switch($boolean) {
                /* Removed */
                case 0:

                    /* Notify the user & immediate leader */
                    $user->notify(new EventParticipantWasRemoved($this, $user, true));
                    $user->supervisor->notify(new EventParticipantWasRemoved($this, $user, false));

                break;
                /* Added */
                case 1:

                    /* Notify the user & immediate leader */
                    $user->notify(new EventParticipantWasAdded($this, $user, true));
                    $user->supervisor->notify(new EventParticipantWasAdded($this, $user, false));                    

                break;
            }
        }
    }

    public function updateTime($isSameTime, $times) {

        /* Remove all time */
        $this->times()->delete();

        /* Update event times */
        if($isSameTime) {

            /* Update date option */
            $this->isSameTime = $isSameTime;

            /* Update start/end time */
            $this->start_time = Carbon::parse($times[0]['start_time']);
            $this->end_time = Carbon::parse($times[0]['end_time']);
            $this->save();

        } else {

            /* Update date option */
            $this->isSameTime = $isSameTime;

            /* Remove start time & end time */
            $this->start_time = null;
            $this->end_time = null;


            /* Create the time per day */
            foreach($times as $key => $time) {
                $this->setTime($time);
            }

            $this->save();
        }
    }   

    public function attend($form) {
            
        /* Check if registration date is not yet over */
        if(Carbon::now()->lte($this->registration_date)) {

            /* Fetch pivot table */
            $eventParticipant = EventParticipant::where([
                'event_id' => $this->id,
                'participant_id' => $form->employee->id
            ])->get();


            /* Check if it doesn't exist */
            if($eventParticipant->count() == 0) {

                /* Set the forms status to approved */
                $form->setAsDraft(1);

                /* Create event connection */
                $eventParticipant = EventParticipant::create([
                    'event_id' => $this->id,
                    'participant_id' => $form->employee->id,
                    'form_id' => $form->id,
                    'approver_id' => $form->employee->getImmediateLeaderID(),
                ]);


                /* Notify the user & immediate leader */
                $form->employee->notify(new EventWillAttend($this, $form->employee, true));
                $form->employee->supervisor->notify(new EventWillAttend($this, $form->employee, false));

                return $eventParticipant;
            }
        }

        return response()->json([
            'response' => 0,
            'message' => "Sorry! You can't register anymore on this event"
        ]);
    }

    public function cancel($participant, $reason) {

        /* Fetch pivot table */
        $eventParticipant = EventParticipant::where([
            'event_id' => $this->id,
            'participant_id' => $participant
        ])->get();


        /* Check if it exists */
        if($eventParticipant->count() > 0) {

            /* Fetch existing participants before updating */
            $existingParticipants = $event->getAttendingParticipants()->pluck('participant_id')->toArray();
            $eventParticipant = $eventParticipant->first();


            /* Cancel the participant's attendance */
            $eventParticipant->cancel($reason);

            /* Update participants */
            $event->updateParticipants(null, $existingParticipants);        
        }
    }

    public function removeParticipants($participants) {

        $user = \Auth::user();
        $existingParticipants = $this->getAttendingParticipants()->pluck('participant_id')->toArray();

        foreach($participants as $participant) {
                
            $eventParticipant = EventParticipant::findOrFail($participant);


            /* Cancel the participant's attendance */
            $eventParticipant->cancel('Cancelled by ' . $user->renderFullname());
        }

        /* Update participants */
        $this->updateParticipants(null, $existingParticipants);
    }

    public function addAttending() {

        $attendingCount = $this->getAttendingParticipants()->count();


        /* Check participant limit */
        if($this->limit == 0 || $this->limit >= ($attendingCount)) {

            /* Add one to the attending */
            $this->attending = $this->attending + 1; 
            $this->save();

            return true;
        }

        return false;
    }

    public static function fetchEvents($events)
    {
        /* Fetch events */
        $eventArray = [];

        /* Create event array */
        foreach ($events as $key => $event) {
            $event = $event instanceof Event ? $event : $event->event;
            
            /* Fetch No. of days */
            $start = Carbon::parse($event->start_date);
            $end = Carbon::parse($event->end_date);
            $totalDays = $event->renderNoOfDays();
            $days = 0;

            while($days != $totalDays) {

                /* Escape if on weekend and weekend isn't toggled as included */
                $canAdd = $start->isWeekend() ? $event->hasWeekend : true;
                if($canAdd) {
                    $startDate = null;
                    $endDate = null;

                    /* Create tmp object */
                    $tmp = new \stdClass();
                    $tmp->id = 'iserve' . $event->id . '' . $days;
                    $tmp->title = $event->title;
                    $tmp->backgroundColor = $event->renderColor();
                    $tmp->textColor = '#' . Helpers::renderTextColor($event->renderColor());
                    $tmp->boderColor = $event->renderColor();
                    $tmp->url = $event->renderAttendURL();

                    /* Check if all days have the same time */
                    if($event->isSameTime) {
                        $startDate = Carbon::parse($start->format('Y-m-d') . ' ' . $event->start_time->format('H:i'));
                        $endDate = Carbon::parse($start->format('Y-m-d') . ' ' . $event->end_time->format('H:i'));

                    } else {
                        $startDate = Carbon::parse($start->format('Y-m-d') . ' ' . $event->times[$days]->start_time->format('H:i'));
                        $endDate = Carbon::parse($start->format('Y-m-d') . ' ' . $event->times[$days]->end_time->format('H:i'));
                    }

                    $tmp->start = $startDate->toDateTimeString();
                    $tmp->googleStart = $startDate->format('Y-m-d\TH:i:sP');
                    $tmp->end = $endDate->toDateTimeString();
                    $tmp->googleEnd = $endDate->format('Y-m-d\TH:i:sP');

                    /* Collect array of events */
                    array_push($eventArray, $tmp);

                    $days++;
                }

                $start->addDays(1);
            }
        }

        return $eventArray;     
    }

    /*
    |-----------------------------------------------
    | @Checker
    |-----------------------------------------------
    */
    public function isRegistered($id) {
        return in_array($id, $this->participants()->pluck('participant_id')->toArray());
    }

    public function canStillRegister() {
        return Carbon::now()->lte($this->registration_date);
    }


    /*
    |-----------------------------------------------
    | @Render
    |-----------------------------------------------
    */
    public function renderNoOfDays() {
        if(!$this->start_date || !$this->end_date)
            return false;


        $hasWeekend = $this->hasWeekend;

        return Carbon::parse($this->start_date)->diffInDaysFiltered(function(Carbon $date) use ($hasWeekend) {
                return $hasWeekend ? true : !$date->isWeekend();
            }, Carbon::parse($this->end_date)) + 1;
    }

    public function renderParticipants() {
        return $this->limit ? $this->attending . ' / ' . $this->limit : 'No Limit';
    }  

    public function renderTime() {
        $time = '';

        /* Check if all days have the same time */
        if($this->isSameTime && $this->start_time && $this->end_time)
            return $this->start_time->format('H:i') . ' - ' . $this->end_time->format('H:i');


        /* Loop through the days */
        foreach($this->times as $key => $eventTime) {
               
            $time .= "Day " . ($key + 1) . " \n";
            $time .= $eventTime->renderTime() . "\n";
        }


        return $time;
    }

    public function renderColor() {
        return '#' . ($this->color ? $this->color : 'FFFFFF');
    }

    public function renderAttendURL() {
        return route('event.createrequest', [
            'event' => $this->id, 
            'form' => $this->form_template->id,
        ]);
    }

    public function renderViewURL() {
        return route('event.show', $this->id);
    }
}
