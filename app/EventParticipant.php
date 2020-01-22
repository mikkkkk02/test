<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Laravel\Scout\Searchable;

use App\EventParticipant;

class EventParticipant extends Model
{
    use Searchable;

    public $asYouType = true;

    /*
    |-----------------------------------------------
    | @Columns
    |-----------------------------------------------
    */
    const TABLE_COLUMNS = [
        'id', 'event_id', 'participant_id', 'form_id', 'approver_id',
        'status', 'hasAttended',
        'created_at', 'approved_at',
    ];


    /*
    |-----------------------------------------------
    | @Status
    |-----------------------------------------------
    */
    const PENDING = 0;
    const APPROVED = 1;
    const DISAPPROVED = 2;
    const CANCELLED = 3;

    /*
    |-----------------------------------------------
    | @Attendance
    |-----------------------------------------------
    */
    const ATTENDED = 1;
    const NOSHOW = 2;


    protected $guarded = [];
    protected $appends = ['extra'];

    public $timestamps = false;


    public function event() {
    	return $this->belongsTo(Event::class);
    }

    public function participant() {
    	return $this->belongsTo(User::class)->withTrashed();
    }

    public function form() {
    	return $this->belongsTo(Form::class)->withTrashed();
    }

    public function approver() {
        return $this->belongsTo(User::class, 'approver_id')->withTrashed();
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
            'title' => $this->event->title,
            'start_date' => $this->event->start_date,
            'end_date' => $this->event->end_date,
            'time' => $this->event->renderTime(),
            'hours' => $this->event->hours,
            'email' => $this->participant->email,
            'participant' => $this->participant->renderFullname(),
            'approver' => $this->approver->renderFullname(),
            'approved_at' => $this->approved_at,
            'status' => $this->renderStatus(),
            'attendance' => $this->renderAttendance(),            
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
            'status' => $this->renderStatus(),
            'attendance' => $this->renderAttendance(),
            'approveurl' => $this->renderApproveURL(),
            'disapproveurl' => $this->renderDisapproveURL(),
            'attendedurl' => $this->renderAttendedURL(),
            'noshowurl' => $this->renderNoShowURL(),
        ];
    }   

    public function setAsPending($save = false) {
        $this->status = EventParticipant::PENDING;

        if($save)
            $this->save();
    }

    public function setAsApproved($save = false) {
        $this->status = EventParticipant::APPROVED;

        if($save)
            $this->save();
    }
    
    public function setAsDisapproved($save = false) {
        $this->status = EventParticipant::DISAPPROVED;

        if($save)
            $this->save();
    }

    public function setAsCancelled($save = false) {
        $this->status = EventParticipant::CANCELLED;

        if($save)
            $this->save();
    }    

    public function setAsAttended($save = false) {
        $this->hasAttended = EventParticipant::ATTENDED;

        if($save)
            $this->save();
    }    

    public function setAsNoShow($save = false) {
        $this->hasAttended = EventParticipant::NOSHOW;

        if($save)
            $this->save();
    }    

    public static function getStatus() {
        return [
            ['label' => 'Pending', 'class' => 'bg-yellow', 'value' => EventParticipant::PENDING],
            ['label' => 'Approved', 'class' => 'bg-green', 'value' => EventParticipant::APPROVED],
            ['label' => 'Disapproved', 'class' => 'bg-red', 'value' => EventParticipant::DISAPPROVED],
            ['label' => 'Cancelled', 'class' => 'bg-orange', 'value' => EventParticipant::CANCELLED],
        ];
    }

    public static function getAttendance() {
        return [
            ['label' => 'Pending', 'class' => 'bg-yellow', 'value' => EventParticipant::PENDING],
            ['label' => 'Attended', 'class' => 'bg-green', 'value' => EventParticipant::ATTENDED],
            ['label' => 'No Show', 'class' => 'bg-red', 'value' => EventParticipant::NOSHOW],
        ];
    }


    /*
    |-----------------------------------------------    
    | @Methods
    |-----------------------------------------------
    */
    public function approve() {

        /* Change status to approved */
        $this->setAsApproved();

        /* Set approver & date */
        $this->approver_id = \Auth::user()->id;
        $this->approved_at = date('Y-m-d H:i:s');
        $this->save();

        return true;
    }

    public function disapprove($reason) {

        /* Change status to approved */
        $this->setAsDisapproved();

        /* Set disapprover & date */
        $this->reason = $reason;
        $this->approver_id = \Auth::user()->id;
        $this->approved_at = date('Y-m-d H:i:s');
        $this->save();

        return true;
    } 

    public function cancel($reason) {

        /* Change status to approved */
        $this->setAsCancelled();

        /* Set reason */
        $this->reason = $reason;
        $this->save();

        return true;
    }        


    /*
    |-----------------------------------------------
    | @Checker
    |-----------------------------------------------
    */   
    public function isPending() {
        return $this->status == EventParticipant::PENDING;
    }

    public function isApprove() {
        return $this->status == EventParticipant::APPROVED;
    }

    public function isDisapprove() {
        return $this->status == EventParticipant::DISAPPROVED;
    }

    public function isCancel() {
        return $this->status == EventParticipant::CANCELLED;
    }    


    /*
    |-----------------------------------------------
    | @Render
    |-----------------------------------------------
    */
    public static function renderStatusFilter() {
        return EventParticipant::renderTableFilter(EventParticipant::getStatus(), 'status');
    }

    public static function renderAttendanceFilter() {
        return EventParticipant::renderTableFilter(EventParticipant::getAttendance(), 'attendance');
    }

    public static function renderTableFilter($array, $string) {
        $array = [];

        /* Add in status options */
        $array[0] = [
            'label' => $string,
            'options' => [],
        ];

        foreach (EventParticipant::getStatus() as $key => $value) {
            array_push($array[0]['options'], [
                'id' => $value['value'],
                'label' => $value['label'],
            ]);
        }


        return $array;
    }

    public function renderStatus() {
        return $this->renderConstantLabel(EventParticipant::getStatus(), $this->status);
    }

    public function renderStatusClass() {
        return $this->renderConstantClass(EventParticipant::getStatus(), $this->status);
    }    

    public function renderAttendance() {
        return $this->renderConstantLabel(EventParticipant::getAttendance(), $this->hasAttended);
    }

    public function renderAttendanceClass() {
        return $this->renderConstantClass(EventParticipant::getAttendance(), $this->hasAttended);
    }

    public function renderConstantLabel($array, $value) {
        $result = $this->renderConstants($array, $value);

        if($result)
            return $result['label'];
    }

    public function renderConstantClass($array, $value) {
        $result = $this->renderConstants($array, $value);

        if($result)
            return $result['class'];
    }

    public function renderConstants($array, $value) {

        /* Loop through the array */
        foreach ($array as $obj) {
            
            if($obj['value'] == $value)
                return $obj;
        }
    }

    public function renderApproveURL() {
        return route('eventparticipant.approve', $this->id);
    }

    public function renderDisapproveURL() {
        return route('eventparticipant.disapprove', $this->id);
    }

    public function renderAttendedURL() {
        return route('eventparticipant.attended', $this->id);
    }

    public function renderNoShowURL() {
        return route('eventparticipant.noshow', $this->id);
    }        
}