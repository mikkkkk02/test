<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;

class EventTime extends Model
{
    /*
    |-----------------------------------------------
    | @Columns
    |-----------------------------------------------
    */
    const MINIMAL_COLUMNS = [
        'id', 'event_id',
        'start_time', 'end_time',
    ];

    protected $guarded = [];
    protected $dates = ['start_time', 'end_time'];

    public function event() {
    	return $this->belongsTo(Event::class);
    }


    /*
    |-----------------------------------------------
    | @Helper
    |-----------------------------------------------
    */
    //


    /*
    |-----------------------------------------------    
    | @Methods
    |-----------------------------------------------
    */
    public function updateTime($time) {
        $this->start_time = Carbon::parse($time['start_time']);
        $this->end_time = Carbon::parse($time['end_time']);
        $this->save();
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
        return $this->start_time->format('H:i') . ' - ' . $this->end_time->format('H:i');
    }
}
