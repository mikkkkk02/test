<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IdpApprover extends Model
{
    /*
    |-----------------------------------------------
    | @Columns
    |-----------------------------------------------
    */
    const MINIMAL_COLUMNS = [
        'id', 'idp_id', 'approver_id',
        'enabled', 'status',
    ];

    /*
    |-----------------------------------------------
    | @Status
    |-----------------------------------------------
    */
    const PENDING = 0;
    const APPROVED = 1;
    const DISAPPROVED = 2;

    /*
    |-----------------------------------------------
    | @Type
    |-----------------------------------------------
    */
    const IMMEDIATE_LEADER = 0;
    const GROUP_HEAD = 1;
    const OD = 2;


    protected $guarded = [];
    protected $appends = ['extra'];


    public function idp() {
    	return $this->belongsTo(Idp::class);
    }

    public function temp() {
        return $this->belongsTo(TempIdp::class, 'idp_id');
    }    

    public function approver() {
        return $this->belongsTo(User::class, 'approver_id')->withTrashed();
    }


    /*
    |-----------------------------------------------
    | @Helper
    |-----------------------------------------------
    */
    public function getExtraAttribute() {
        return $this->attributes['extra'] = [
            'type' => $this->renderType(),
            'fullname' => $this->renderFullname(),
        ];
    }   

    public function setAsPending() {
        $this->status = FormApprover::PENDING;
        $this->save();
    }

    public function setAsApprove() {
        $this->status = FormApprover::APPROVED;

        /* Disable */
        $this->setAsDisabled();
    }

    public function setAsDispprove() {
        $this->status = FormApprover::DISAPPROVED;
        $this->save();
    }  

    public function setAsEnabled() {
        $this->enabled = 1;
        $this->save();
    }

    public function setAsDisabled() {
        $this->enabled = 0;
        $this->save();
    }    

    public static function getStatus() {
        return [
            ['label' => 'Pending', 'color' => 'bg-yellow', 'value' => IdpApprover::PENDING],
            ['label' => 'Approved', 'color' => 'bg-green', 'value' => IdpApprover::APPROVED],
            ['label' => 'Disapproved', 'color' => 'bg-red', 'value' => IdpApprover::DISAPPROVED],
        ];
    }

    public static function getTypes() {
        return [
            ['label' => 'Immediate Leader', 'value' => IdpApprover::IMMEDIATE_LEADER],
            ['label' => 'Group Head', 'value' => IdpApprover::GROUP_HEAD],
            ['label' => 'OD', 'value' => IdpApprover::OD],
        ];
    }           


    /*
    |-----------------------------------------------    
    | @Methods
    |-----------------------------------------------
    */
    public function approve() {
        $this->setAsApprove();
    }

    public function disapprove($reason) {
        $this->reason = $reason;
        $this->setAsDispprove();
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
    public function renderStatus() {
        return $this->renderConstantLabel(IdpApprover::getStatus(), $this->status);
    }

    public function renderStatusColor() {
        return $this->renderConstantColor(IdpApprover::getStatus(), $this->status);
    }

    public function renderType() {
        return $this->renderConstantLabel(IdpApprover::getTypes(), $this->type);
    }

    public function renderConstantLabel($array, $value) {
        $result = $this->renderConstants($array, $value);

        if($result)
            return $result['label'];
    }

    public function renderConstantColor($array, $value) {
        $result = $this->renderConstants($array, $value);

        if($result)
            return $result['color'];
    }    

    public function renderConstants($array, $value) {

        /* Loop through the array */
        foreach ($array as $obj) {
            
            if($obj['value'] == $value)
                return $obj;
        }
    }

    public function renderFullname() {
        return $this->approver->renderFullname();
    }
}
