<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LearningParticipant extends Model
{
    /*
    |-----------------------------------------------
    | @Columns
    |-----------------------------------------------
    */
    const TABLE_COLUMNS = [
        'id', 'learning_id', 'participant_id', 'form_id',
        'status', 'hasAttended',
    ];

    /*
    |-----------------------------------------------
    | @Status
    |-----------------------------------------------
    */
    const PENDING = 0;
    const APPROVED = 1;
    const DISAPPROVED = 2;
    const CANCELLED = 2;

    protected $guarded = [];

    public $timestamps = false;


    public function learning() {
    	return $this->belongsTo(Learning::class);
    }

    public function participant() {
    	return $this->belongsTo(User::class)->withTrashed();
    }

    public function charge_to() {
    	return $this->belongsTo(Department::class, 'charge_to');
    }

    public function approver() {
        return $this->belongsTo(User::class, 'approver_id')->withTrashed();
    }  


    /*
    |-----------------------------------------------
    | @Helper
    |-----------------------------------------------
    */
    public function setAsPending($save = false) {
        $this->status = LearningParticipant::PENDING;

        if($save)
            $this->save();
    }

    public function setAsApproved($save = false) {
        $this->status = LearningParticipant::APPROVED;

        if($save)
            $this->save();        
    }
    
    public function setAsDisapproved($save = false) {
        $this->status = LearningParticipant::DISAPPROVED;

        if($save)
            $this->save();        
    }

    public function setHasAttended($boolean) {
        $this->hasAttended = $boolean;
    }

    public static function getStatus() {
        return [
            ['label' => 'Pending', 'value' => EventParticipant::PENDING],
            ['label' => 'Approved', 'value' => EventParticipant::APPROVED],
            ['label' => 'Disapproved', 'value' => EventParticipant::DISAPPROVED],
            ['label' => 'Cancelled', 'value' => EventParticipant::CANCELLED],
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

    public function disapprove() {

        /* Change status to approved */
        $this->setAsDisapproved(1);

        return true;
    }


    /*
    |-----------------------------------------------
    | @Checker
    |-----------------------------------------------
    */   
    public function isPending() {
        return $this->status == LearningParticipant::PENDING;
    }

    public function isApprove() {
        return $this->status == LearningParticipant::APPROVED;
    }

    public function isDisapprove() {
        return $this->status == LearningParticipant::DISAPPROVED;
    }

    public function isCancelled() {
        return $this->status == LearningParticipant::CANCELLED;
    }    


    /*
    |-----------------------------------------------
    | @Render
    |-----------------------------------------------
    */   
    public static function renderTableFilter() {
        $array = [];

        /* Add in status options */
        $array[0] = [
            'label' => 'status',
            'options' => [],
        ];

        foreach (LearningParticipant::getStatus() as $key => $value) {
            array_push($array[0]['options'], [
                'id' => $value['value'],
                'label' => $value['label'],
            ]);
        }


        return $array;
    }   

    public function renderStatus() {
        return '';
    }  
}