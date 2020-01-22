<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Laravel\Scout\Searchable;

use App\LearningTime;

class Learning extends Model
{
    use Searchable;

    /*
    |-----------------------------------------------
    | @Columns
    |-----------------------------------------------
    */
    const MINIMAL_COLUMNS = [
        'id', 'form_template_id', 'learning_category_id',
        'title', 'description', 'venue', 'facilitator', 'provider', 'hours', 'limit', 'attending',
        'start_date', 'end_date',
    ];

    /*
    |-----------------------------------------------
    | @Type
    |-----------------------------------------------
    */
    const INORDER = 0;
    const SIMULTANEOUSLY = 1;

    protected $guarded = [];
    protected $appends = ['extra'];


    public function form() {
        return $this->belongsTo(FormTemplate::class)->withTrashed();
    }

    public function category() {
    	return $this->belongsTo(LearningCategory::class);
    }

    public function times() {
        return $this->belongsTo(EventTime::class);
    }

    public function approvers() {
        return $this->hasMany(LearningApprover::class);
    }

    public function creator() {
    	return $this->belongsTo(User::class, 'creator_id')->withTrashed();
    }

    public function updater() {
    	return $this->belongsTo(User::class, 'updater_id')->withTrashed();
    }


    /*
    |-----------------------------------------------
    | @Helper
    |-----------------------------------------------
    */
    public function getExtraAttribute() {
        return $this->attributes['extra'] = [
            'time' => $this->renderTime(),
            'view' => $this->renderViewURL(),
        ];
    }   

    public function setTime($time) {

        /* Create time */
        LearningTime::create([
            'learning_id' => $this->id,

            'start_time' => $time['start_time'],
            'end_time' => $time['end_time'],
        ]);
    }


    /*
    |-----------------------------------------------    
    | @Methods
    |-----------------------------------------------
    */
    public function updateTime($isSameTime, $times) {

        /* Update event times */
        if($isSameTime) {

            /* Update date option */
            $this->isSameTime = $isSameTime;

            /* Update start/end time */
            $this->start_time = $times[0]['start_time'];
            $this->end_time = $times[0]['end_time'];
            $this->save();

        } else {

            /* Update date option */
            $this->isSameTime = $isSameTime;

            /* Remove start time & end time */
            $this->start_time = null;
            $this->end_time = null;


            /* Create the time per day */
            foreach($times as $time) {
                $this->setTime($time);
            }

            $this->save();
        }
    }     
   
    public function attend($participant, $charge = null) {
            
        /* Create event connection */
        LearningParticipant::where([
            'event_id' => $this->id,
            'participant_id' => $participant->id,

            'charge_to' => $charge || $this->participant->department->id,
        ]);
    }    
    
    public function cancel($participant) {

        /* Fetch pivot table */
        $learningParticipant = LearningParticipant::where([
            'event_id' => $this->id,
            'participant_id' => $participant->id
        ])->get();


        /* Check if it exists */
        if($learningParticipant->count() > 0)
            $learningParticipant->first()->delete();
    } 

    public function addParticipant($participants) {

        foreach ($participants as $participant) {
            $user = User::findOrFail($participant);

            /* Add employee to participants */
            $this->attend($user);

            // Notification for user & immediate leader
        }
    }

    public function removeParticipant($participants) {

        foreach ($participants as $participant) {
            $user = User::findOrFail($participant);

            /* Remove employee to participants */
            $this->cancel($user);

            // Notification for user
        }
    }    


    /*
    |-----------------------------------------------
    | @Checker
    |-----------------------------------------------
    */   
    //


    /*
    |-----------------------------------------------
    | @Render
    |-----------------------------------------------
    */   
    public function renderTime() {
        return '';
    }

    public function renderViewURL() {
        return route('showlearning', $this->id);
    }    
}
