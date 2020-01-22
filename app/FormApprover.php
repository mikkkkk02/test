<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;

class FormApprover extends Model
{
    /*
    |-----------------------------------------------
    | @Columns
    |-----------------------------------------------
    */
    const MINIMAL_COLUMNS = [
        'id', 'form_id', 'approver_id',
        'type', 'type_value',
        'enabled', 'status',
        'reason',
        'approved_date',
    ];

    /*
    |-----------------------------------------------
    | @Status
    |-----------------------------------------------
    */
    const PENDING = 0;
    const APPROVED = 1;
    const DISAPPROVED = 2;
    const STOP = 3;

    protected $guarded = [];
    protected $appends = ['extra'];


    public function form() {
    	return $this->belongsTo(Form::class)->withTrashed();
    }

    public function form_template_approver() {
        return $this->belongsTo(FormTemplateApprover::class, 'form_template_approver_id');
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
        ];
    }   

    public function setAsPending() {
        $this->status = FormApprover::PENDING;
        $this->save();
    }

    public function setAsApprove() {
        $this->approved_date = Carbon::now();
        $this->status = FormApprover::APPROVED;

        /* Disable */
        $this->setAsDisabled();
    }

    public function setAsDispprove() {
        $this->approved_date = Carbon::now();
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
            ['label' => 'Pending', 'color' => 'bg-yellow', 'value' => Form::PENDING],
            ['label' => 'Approved', 'color' => 'bg-green', 'value' => Form::APPROVED],
            ['label' => 'Disapproved', 'color' => 'bg-red', 'value' => Form::DISAPPROVED],
        ];
    }       


    /*
    |-----------------------------------------------    
    | @Methods
    |-----------------------------------------------
    */
    public function approve($reason = null) {
        $this->reason = $reason;
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
        return $this->renderConstantLabel(FormApprover::getStatus(), $this->status);
    }

    public function renderStatusColor() {
        return $this->renderConstantColor(FormApprover::getStatus(), $this->status);
    }

    public function renderType() {
        switch($this->type) {
            case FormTemplateApprover::LEVEL: return $this->renderConstantLabel(FormTemplateApprover::getLevels(), $this->type_value);
            case FormTemplateApprover::EMPLOYEE: return $this->renderConstantLabel(FormTemplateApprover::getTypes(), $this->type);  
            case FormTemplateApprover::COMPANY: return $this->renderConstantLabel(FormTemplateApprover::getCompanies(), $this->type_value);
            case FormTemplateApprover::CEO: return 'CEO';
            case FormTemplateApprover::GROUP_HEAD: return 'Group Head';
            default: return '';
        }
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
}
