<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Laravel\Scout\Searchable;
use Carbon\Carbon;

use App\Form;
use App\FormTemplateField;
use App\FormHistory;
use App\TempFormAnswer;

class TempForm extends Model
{
    use Searchable;
    use SoftDeletes; 

    public $asYouType = true;

    /*
    |-----------------------------------------------
    | @Columns
    |-----------------------------------------------
    */
    const MINIMAL_COLUMNS = [
        'id', 'form_template_id', 'ticket_id', 'employee_id', 'assignee_id',

        'form_id', 'requester_id',
        'status', 'reason',
        'approved_date',
    ];
    const TABLE_COLUMNS = [
        'temp_forms.id', 'temp_forms.form_template_id', 'temp_forms.ticket_id', 'temp_forms.employee_id', 'temp_forms.assignee_id',
        'temp_forms.purpose',

        'temp_forms.mr_title', 'temp_forms.mr_date', 'temp_forms.mr_start_time', 'temp_forms.mr_end_time',
        'temp_forms.isLocal', 'temp_forms.course_cost', 'temp_forms.accommodation_cost', 'temp_forms.meal_cost', 'temp_forms.transport_cost', 'temp_forms.others_cost', 'temp_forms.total_cost',

        'temp_forms.created_at', 'temp_forms.updated_at',

        'temp_forms.form_id', 'temp_forms.requester_id',
        'temp_forms.status', 'temp_forms.reason',
        'temp_forms.approved_date',
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


    public function form() {
    	return $this->belongsTo(Form::class)->withTrashed();
    }

    public function template() {
    	return $this->belongsTo(FormTemplate::class, 'form_template_id')->withTrashed();
    }

    public function history() {
        return $this->hasOne(FormHistory::class);
    }        

    public function ticket() {
    	return $this->belongsTo(Ticket::class);
    }

    public function employee() {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function assignee() {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function answers() {
        return $this->hasMany(TempFormAnswer::class);
    }

    public function approvers() {
        return $this->hasMany(TempFormApprover::class);
    }

    public function creator() {
        return $this->belongsTo(User::class, 'creator_id')->withTrashed();
    }

    public function requester() {
        return $this->belongsTo(User::class, 'requester_id')->withTrashed();
    }

    public function mr_reservation() {
        return $this->hasOne(TempMrReservation::class, 'form_id');
    }

    protected $guarded = [];
    protected $appends = ['extra'];


    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        $searchable = [
            'id' => $this->id,
            'type' => $this->template->name,
            'purpose' => $this->purpose,
            'ticket_no' => $this->renderTicket(),
            'requested_by' => $this->employee->renderFullname(),
            'updated_by' => $this->employee->renderFullname(),
            'status' => $this->renderStatus(),
            'pending_at' => $this->renderApprover(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
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
            'answers' => '',
            'approvers' => $this->renderApprover(),
            'ticket' => $this->renderTicket(),
            'status' => $this->renderStatus(),
            'details' => $this->renderDetailsURL(),
            'view' => $this->renderViewURL(),
        ];
    }

    public function setAsApproved($reason = null) {

        /* Update the original form */
        $this->updateForm();

    	$this->reason = $reason;
    	$this->status = TempForm::APPROVED;
    	$this->save();
    }

    public function setAsDisapproved($reason) {
    	$this->reason = $reason;
    	$this->status = TempForm::DISAPPROVED;
    	$this->save();
    }

    public static function getStatus() {
        return [
            ['label' => 'Pending', 'value' => TempForm::PENDING],
            ['label' => 'Approved', 'value' => TempForm::APPROVED],
            ['label' => 'Disapproved', 'value' => TempForm::DISAPPROVED],
            ['label' => 'Cancelled', 'value' => TempForm::CANCELLED],
        ];
    }


    /*
    |-----------------------------------------------
    | @Methods
    |-----------------------------------------------
    */
    public function updateForm() {
        /* Create form history */
        $history = FormHistory::create([
                'form_id' => $this->form_id,
                'temp_form_id' => $this->id,

                'purpose' => $this->form->purpose,

                'isLocal' => $this->form->isLocal,
                'course_cost' => $this->form->course_cost,
                'accommodation_cost' => $this->form->accommodation_cost,
                'meal_cost' => $this->form->meal_cost,
                'transport_cost' => $this->form->transport_cost,
                'others_cost' => $this->form->others_cost,
                'total_cost' => $this->form->total_cost,
            ]);
        /* Save old answers */
        $history->setAnswers();


        /* Update current answer */
        foreach ($this->answers as $key => $answer) {

            $currAnswer = $this->form->answers()
                                    ->where('form_id', $this->form_id)
                                    ->where('form_template_field_id', $answer->form_template_field_id)
                                    ->get();

            if($currAnswer->count() > 0) {

                /* Update answer */
                $currAnswer = $currAnswer->first();
                $currAnswer->value = $answer->value;
                $currAnswer->value_others = $answer->value_others;
                $currAnswer->save();

            } else {

                $currAnswer = FormAnswer::create([
                        'form_id' => $this->form_id,
                        'form_template_field_id' => $answer->id,
                        'value' => $answer->value,
                        'value_others' => $answer->value_others,
                    ]);
            }
        }


        /* Update field on form */
        $this->form->purpose = $this->purpose;

        /* Add in L&D rules */
        if($this->template->category->forLearning()) {

            $this->form->isLocal = $this->isLocal;
            $this->form->course_cost = $this->course_cost;
            $this->form->accommodation_cost = $this->accommodation_cost;
            $this->form->meal_cost = $this->meal_cost;
            $this->form->transport_cost = $this->transport_cost;
            $this->form->others_cost = $this->others_cost;
            $this->form->total_cost = $this->total_cost;
        }

        /* Add in Meeting Room rules */
        if($this->template->isMeetingRoom()) {
            $history->temp_mr_reservation_id = $this->mr_reservation->id;
            $history->save();
            
            $mrReservation = $this->form->mr_reservation;

            $history->setMrReservation();

            $mrReservation->name = $this->mr_reservation->name;
            $mrReservation->description = $this->mr_reservation->description;
            $mrReservation->color = $this->mr_reservation->color;
            $mrReservation->save();

            $mrReservation->mr_reservation_times()->delete();

            foreach ($this->mr_reservation->mr_reservation_times as $mrReservationTime) {
                $mrReservation->mr_reservation_times()->create([
                    'mr_reservation_id' => $mrReservation->id,
                    'date' => $mrReservationTime->date,
                    'start_time' => $mrReservationTime->start_time,
                    'end_time' => $mrReservationTime->end_time,
                    'creator_id' => $mrReservationTime->creator_id,
                    'updater_id' => $mrReservationTime->updater_id,
                ]);
            }

        }

        /* Update ticket on form */
        $this->form->ticket->updateSLA($this->form->getSLAStartDate());


        $this->form->save();
    }   

   	public function updateStatus($status, $reason = null) {
        return $this->form->updateStatus($status, $reason, $this);
   	}

    public function addAnswers($fields) {
        $this->form->addAnswers($fields, $this);
    }

    public function addApprovers() {
        $this->form->addApprovers($this);

        /* Check if no approver */
        if($this->checkIfAllApprove()) {

            /* Set as approved */
            $this->setAsApproved();

            return true;
        }

        return false;
    }


    /*
    |-----------------------------------------------
    | @Checker
    |-----------------------------------------------
    */
    public function checkIfAllApprove() {
        return !$this->approvers()->where('status', FormApprover::PENDING)->exists();
    }

    public function isApproved() {
        return $this->status == TempForm::APPROVED;
    }

    public function isApprover($approver) {
        return $this->approvers()->where('approver_id', $approver->id)->exists();
    }

    public function isEditable() {
        return $this->form->isEditable();
    }    


    /*
    |-----------------------------------------------
    | @Render
    |-----------------------------------------------
    */    	
    public function renderTicket() {
        return $this->form->renderTicket();
    }   

    public function renderApprover() {
        return $this->form->renderApprover($this);
    }

    public function renderStatus() {
        return $this->renderConstantLabel(TempForm::getStatus(), $this->status);
    }

    public static function renderTableFilter() {
        $array = [];

        /* Add in status filters */
        array_push($array, TempTicketUpdate::renderConstantTable(TempTicketUpdate::getStatus(), 'status'));


        return $array;
    }

    public static function renderConstantTable($constant, $label) {
        $array = [];

        /* Add in status options */
        $array = [
            'label' => $label,
            'options' => [],
        ];

        foreach ($constant as $key => $value) {
            array_push($array['options'], [
                'id' => $value['value'],
                'label' => $value['label'],
            ]);
        }


        return $array;
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

    public function renderDetailsURL() {
        return route('temprequest.fetchanswers', $this->id);
    }

    public function renderViewURL() {
        return route('temprequest.show', $this->id);
    }   
}
