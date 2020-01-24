<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Notifications\Requests\RequestWasDisapproved;
use App\Notifications\Requests\RequestWasApproved;
use App\Notifications\Requests\RequestHasApprover;
use App\Notifications\Requests\RequestApprovalWasUpdated;
use App\Notifications\Requests\RequestHasUpdate;

use App\Notifications\Tickets\TicketHasTechnician;

use Laravel\Scout\Searchable;
use Carbon\Carbon;

use App\FormApprover;
use App\FormAnswer;
use App\FormLog;
use App\FormTemplateField;
use App\FormTemplateApprover;
use App\FormTemplateCategory;
use App\Ticket;
use App\TempAttachment;
use App\Settings;

class Form extends Model
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
    ];   
    const TABLE_COLUMNS = [
        'forms.id', 'forms.form_template_id', 'forms.ticket_id', 'forms.employee_id', 'forms.assignee_id',
        'forms.purpose', 'forms.status', 

        'forms.mr_title', 'forms.mr_date', 'forms.mr_start_time', 'forms.mr_end_time',
        'forms.isLocal', 'forms.course_cost', 'forms.accommodation_cost', 'forms.meal_cost', 'forms.transport_cost', 'forms.others_cost', 'forms.total_cost',

        'forms.created_at', 'forms.updated_at',
    ];

    /*
    |-----------------------------------------------
    | @Status
    |-----------------------------------------------
    */
    const PENDING = 0;
    const APPROVED = 1; 
    const DISAPPROVED = 2;
    const DRAFT = 3;
    const CANCELLED = 4;

    protected $guarded = [];
    protected $appends = ['extra'];


    public function template() {
    	return $this->belongsTo(FormTemplate::class, 'form_template_id')->withTrashed();
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

    public function event() {
    	return $this->hasOne(EventParticipant::class);
    }

    public function learning() {
        return $this->hasOne(LearningParticipant::class);
    }  

    public function answers() {
        return $this->hasMany(FormAnswer::class);
    }

    public function approvers() {
        return $this->hasMany(FormApprover::class);
    }

    public function logs() {
        return $this->hasMany(FormLog::class);
    }    

    public function attachments() {
        return $this->hasMany(FormAttachment::class);
    }

    public function history() {
        return $this->hasMany(FormHistory::class);
    }

    public function updates() {
        return $this->hasMany(FormUpdate::class);
    }

    public function form_updates() {
        return $this->hasMany(TempForm::class);
    }

    public function mr_reservation() {
        return $this->hasOne(MrReservation::class, 'form_id');
    }

    public function creator() {
        return $this->belongsTo(User::class, 'creator_id')->withTrashed();
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
            'type' => $this->template->name,
            'purpose' => $this->purpose,
            'ticket_no' => $this->renderTicket(),
            'requested_by' => $this->employee->renderFullname(),
            'status' => $this->renderStatus(),
            'assigned_to' => $this->technician ? $this->technician->renderFullname() : '',
            'pending_at' => $this->renderApprover(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'date_closed' => $this->ticket ? $this->ticket->date_closed : '',
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
            'loading' => false,
            'answers' => '',
            'total' => $this->renderTotalCost(),
            'approvers' => $this->renderApprover(),
            'ticket' => $this->renderTicket(),
            'status' => $this->renderStatus(),
            'details' => $this->renderDetailsURL(),
            'fetchlogs' => $this->renderFetchLogsURL(),
            'view' => $this->renderViewURL(),
        ];
    }

    public function getSLAStartDate() {

        /* Check SLA Option */
        if(!$this->template->sla_option)
            return Carbon::now();
        
        if($this->template->sla_option && $this->template->sla_date_id) {

            /* Check SLA type */
            switch ($this->template->sla_type) {
                case 0:
                    /* Fetch answer */
                    $dateField = FormAnswer::where([
                                    ['form_id', $this->id],
                                    ['form_template_field_id', $this->template->sla_date_id],
                                ])->get()->first();


                    /* Check if datefield exists */
                    if($dateField)
                        return Carbon::parse($dateField->value);

                break;
                case 1;
                    $dateField = null;

                    /* Fetch answer */
                    $tableField = FormAnswer::where([
                                    ['form_id', $this->id],
                                    ['form_template_field_id', $this->template->sla_date_id],
                                ])->get()->first();

                    $jsonAnswer = json_decode($tableField->value);
                    if(is_array($jsonAnswer) && count($jsonAnswer)) {

                        $answer = $this->template->sla_row_id ? $jsonAnswer[count($jsonAnswer) - 1] : $jsonAnswer[0];

                        if(isset($answer->{$this->template->sla_col_id}))
                            $dateField = $answer->{$this->template->sla_col_id};

                        /* Check if datefield exists */
                        if($dateField) {
                            return Carbon::parse($dateField);                        
                        }
                    }

                break;                
            }
        }

        return Carbon::now();
    }

    public function setAsCancelled($save = false) {
        $this->status = Form::CANCELLED;
        $this->isResubmitting = true;

        if($save)
            $this->save();
    }

    public function setAsDraft($save = false) {
        $this->status = Form::DRAFT;

        if($save)
            $this->save();
    }

    public function setAsPending($save = false) {
        $this->status = Form::PENDING;

        if($save)
            $this->save();
    }

    public function setAsApproved($save = false) {
        $this->status = Form::APPROVED;


        switch ($this->template->category->id) {
            case FormTemplateCategory::EVENT: break;
            case FormTemplateCategory::LD: case FormTemplateCategory::FORM:

                /* Check if the form already have a ticket */
                if(!$this->ticket) {
                    $this->generateTicket();
                } else {

                    /* Set ticket to open */
                    $this->ticket->setAsOpen(true);
                    /* Update ticket SLA */
                    $this->ticket->updateSLA($this->getSLAStartDate());

                    /* Set technician */
                    $technicians = $this->getCompanyTechnicians();

                    /* Set to the vars array */
                    if($technicians && $technicians->count() > 0)
                        $this->ticket->assignTechnician($technicians->first());
                }

            break;
        }


        /* Notify everyone involved */
        $this->approvalNotification();

        if($save)
            $this->save();
    }

    public function setAsDisapproved($save = false) {
        $this->status = Form::DISAPPROVED;

        if($save)
            $this->save();
    }

    public static function getStatus() {
        return [
            ['label' => 'Cancelled', 'value' => Form::CANCELLED],
            ['label' => 'Draft', 'value' => Form::DRAFT],
            ['label' => 'Pending', 'value' => Form::PENDING],
            ['label' => 'Approved', 'value' => Form::APPROVED],
            ['label' => 'Disapproved', 'value' => Form::DISAPPROVED],
        ];
    }

    public function getCompanyTechnicians() {
        /* Fetch technician for the company */
        if($this->employee->getCompany()) {

            $company = $this->employee->getCompany();

            /* Check if employee has company */
            if($company) {

                /* Fetch technician depending on type */
                switch ($this->template->type) {
                    case FormTemplate::ADMIN: return $company->admin_technicians;
                    case FormTemplate::HR: return $company->hr_technicians; 
                    case FormTemplate::OD: return $company->od_technicians;
                }
            }
        }
    }

    public function getCompanyHR() {
        /* Fetch technician for the company */
        if($this->employee->getCompany()) {

            $company = $this->employee->getCompany();

            /* Check if employee has company */
            if($company)
                return $company->hr;
        }

        return false;
    }

    public function getCompanyOD() {
        /* Fetch technician for the company */
        if($this->employee->getCompany()) {

            $company = $this->employee->getCompany();

            /* Check if employee has company */
            if($company)
                return $company->od;
        }

        return false;        
    }        


    /*
    |-----------------------------------------------
    | @Methods
    |-----------------------------------------------
    */
    public function generateTicket() {
        $vars = [];
        $technicians = $this->getCompanyTechnicians();

        /* Set variable array */
        $vars['form_id'] = $this->id;
        $vars['employee_id'] = $this->employee_id;
        $vars['priority'] = $this->template->priority;
        $vars['start_date'] = $this->getSLAStartDate();


        /* Set to the vars array */
        if($technicians && $technicians->count() > 0)
            $vars['technician_id'] = $technicians->first()->id;


        /* Create the ticket */
        $ticket = Ticket::create($vars);


        /* Check if there is an assigned technicians */
        if($technicians)
            $ticket->assignTechnician($technicians->first());


        /* Link to the form */
        $this->ticket()->associate($ticket);

        /* Create log */
        $this->createLog($this->employee, "Generate ticket ", $this->renderViewURL());
    }

    public function updateStatus($status, $reason = null, $tempForm = null) {


        $approver = \Auth::user();
    
        /* Check if this is for draft */
        if(!$tempForm && $this->status == Form::DRAFT) {

            /* Set as Pending */
            $this->setAsPending(1);

            /* Create the other form approver */
            $this->addApprovers();

        } else {

            /* Check if you're one of the approver */
            $isApprover = $tempForm ? $tempForm->isApprover($approver) : $this->isApprover($approver);
            if(!$isApprover) {
                return false;
            }

           

            /* Get the approver object for the current user */
            $formApprover = $tempForm ? $tempForm->approvers()->where('approver_id', $approver->id)->where('status', 0)->get()->first() :
                                        $this->approvers()->where('approver_id', $approver->id)->where('status', 0)->get()->first();

            $currApprover = $tempForm ? $tempForm->approvers()->where('status', FormApprover::PENDING)->orderBy('sort')->get()->first() :
                                        $this->approvers()->where('status', FormApprover::PENDING)->orderBy('sort')->get()->first(); 
    
            $nextApprover = !$currApprover ? null : 
                                    ($tempForm ? $tempForm->approvers()->where('status', FormApprover::PENDING)->where('id', '!=', $currApprover->id)->orderBy('sort')->get()->first() :
                                                $this->approvers()->where('status', FormApprover::PENDING)->where('id', '!=', $currApprover->id)->orderBy('sort')->get()->first());
                                    
            ##select all next approver                        
            $allNextApprover = !$currApprover ? null : 
                                    ($tempForm ? $tempForm->approvers()->where('status', FormApprover::PENDING)->where('id', '!=', $currApprover->id)->orderBy('sort')->get() :
                                                $this->approvers()->where('status', FormApprover::PENDING)->where('id', '!=', $currApprover->id)->orderBy('sort')->get());               

                                  
            /* Check if the form template approval flow is "In Order" */
            if($this->template->isInOrder()) {

                /* Check if this is the approver */
                if(!$currApprover || $currApprover->id != $formApprover->id) {
                    return false;
                }

            }


            /* Update the status */
            switch ($status) {
                case FormApprover::APPROVED:
                    
                    /* Update the approvers status to approve and disable it now */
                    $formApprover->approve($reason);

                    if($this->template->isManagerial()){

                        if($currApprover->approver_id == $approver->id){

                            if($approver->job_level == 2){

                                if($tempForm) {
    
                                    $tempForm->setAsApproved();
                                    
                                } else {
                                  
                                       
                                    $this->setAsApproved(1);
                                    
                                    /* Notify the employee */
                                    $this->employee->notify(new RequestApprovalWasUpdated($this, $approver));                           
                                }

                                ##update all next approver's status to 3
                                foreach($allNextApprover as $allNext){

                                    $allNext->status = FormApprover::STOP;
                                    $allNext->update();

                                }
                               
                            }else{

                                if(($tempForm || $this->template->isInOrder()) && $nextApprover) {

                          
                                    $nextApprover->setAsEnabled();
                                    
                                    
                                    /* Check if next approver is the current one too */
                                    $dupliApprover = $tempForm ? $tempForm->approvers()->where('approver_id', $approver->id)->where('status', 0)->get() : 
                                                                $this->approvers()->where('approver_id', $approver->id)->where('status', 0)->get();
                                     
                                    if($dupliApprover->count()) {

                                        /* Approve other approvals */
                                        foreach($dupliApprover as $key => $approval) {
                                            $approval->approve($reason);
                                        }
                                        

                                    } else {

                                        if($tempForm) {
                                            /* Notify the next approver */
                                            $nextApprover->approver->notify(new RequestHasUpdate($tempForm, $nextApprover));                                
                                        } else {
                                            /* Notify the next approver */
                                            $nextApprover->approver->notify(new RequestHasApprover($this, $formApprover));   
                                        }
                                    }
                                }

                                if($tempForm) {
                                    /* Check if all approval is done */
                                    if($tempForm->checkIfAllApprove()) {
                                        /* Update the form as approved */
                                        $tempForm->setAsApproved();
                                    }
                                } else {
                                  

                                    /* Check if all approval is done */
                                    if($this->checkIfAllApprove()) {
                                        /* Update the form as approved */
                                        $this->setAsApproved(1);
                                    }

                                    /* Notify the employee */
                                    $this->employee->notify(new RequestApprovalWasUpdated($this, $approver));                           
                                }

                            }
                           
                        }
                    
                    }
                    else{
                        
                        /* Enable the next approver */
                        if(($tempForm || $this->template->isInOrder()) && $nextApprover) {

                          
                                $nextApprover->setAsEnabled();
                            

                            /* Check if next approver is the current one too */
                            $dupliApprover = $tempForm ? $tempForm->approvers()->where('approver_id', $approver->id)->where('status', 0)->get() : 
                                                        $this->approvers()->where('approver_id', $approver->id)->where('status', 0)->get();
                             
                            if($dupliApprover->count()) {

                                /* Approve other approvals */
                                foreach($dupliApprover as $key => $approval) {
                                    $approval->approve($reason);
                                }
                                

                            } else {

                                if($tempForm) {
                                    /* Notify the next approver */
                                    $nextApprover->approver->notify(new RequestHasUpdate($tempForm, $nextApprover));                                
                                } else {
                                    /* Notify the next approver */
                                    $nextApprover->approver->notify(new RequestHasApprover($this, $formApprover));   
                                }
                            }
                        }

                        if($tempForm) {
                            /* Check if all approval is done */
                            if($tempForm->checkIfAllApprove()) {
                                /* Update the form as approved */
                                $tempForm->setAsApproved();
                            }
                        } else {
                          

                            /* Check if all approval is done */
                            if($this->checkIfAllApprove()) {
                                /* Update the form as approved */
                                $this->setAsApproved(1);
                            }

                            /* Notify the employee */
                            $this->employee->notify(new RequestApprovalWasUpdated($this, $approver));                           
                        }
                    }



                break;
                case FormApprover::DISAPPROVED:

                    /* Update the approvers status to disapprove */
                    $formApprover->disapprove($reason);


                    /* Check if all approval is done */
                    if($this->template->isInOrder()) {

                        /* Change status to disapprove */
                        if($tempForm) {
                            $tempForm->setAsDisapproved($reason);
                        } else {
                            $this->setAsDisapproved(true);
                        }

                    } else {
                        if($this->checkIfAllApprove()) {
                            /* Update the form as approved */
                            $this->setAsApproved(1);
                        }
                    }


                    if($tempForm) {

                    } else {
                        /* Notify the employee */
                        $this->employee->notify(new RequestWasDisapproved($this, $approver, $reason));
                    }

                break;
            }
        }

        return true;
    }

    public function approvalNotification() {

        /* Notify the employee */
        // $this->employee->notify(new RequestWasApproved($this, true));

        /* Notify the admin */
        foreach ($this->template->contacts as $contact) {
            $contactEmployee = null;

            /* Fetch the contact */
            switch($contact->type) {
                case FormTemplateApprover::LEVEL:

                    /* Fetch contact by level */
                    switch($contact->type_value) {
                        case FormTemplateApprover::IMMEDIATE_LEADER: 

                            $contactEmployee = $this->employee->getImmediateLeader();         

                        break;
                        case FormTemplateApprover::NEXT_LEVEL_LEADER: 

                            $contactEmployee = $this->employee->getNextLevelLeader();         

                        break;                            
                    }

                break;
                case FormTemplateApprover::EMPLOYEE:
                
                    $contactEmployee = $contact->employee;

                break;
                case FormTemplateApprover::COMPANY:
                
                    /* Fetch contact by type */
                    switch($contact->type_value) {
                        case FormTemplateApprover::HR: 

                            $contactEmployee = $this->getCompanyHR();

                        break;
                        case FormTemplateApprover::OD:

                            $contactEmployee = $this->getCompanyOD();
                                
                        break;                            
                    }                        

                break;                    
            }

            /* Send notif if employee is not null */
            if($contactEmployee)
                $contactEmployee->notify(new RequestWasApproved($this, false));
        }
    }

    public function addAnswers($answers, $tempForm = null) {
    
        /* Loop each form template fields */
        foreach($this->template->fields as $key => $field) {
            $vars = [];

            /* Create the variable array */
            $vars['form_id'] = $this->id;
            $vars['form_template_field_id'] = $field->id;


            /* No need to create if the type is header & paragraph */
            if($field->type != FormTemplateField::HEADER && $field->type != FormTemplateField::PARAGRAPH) {

                $value = isset($answers[$field->id]) ? $answers[$field->id] : '';


                /* Set the answer value */
                switch($field->type) {
                    case FormTemplateField::RADIOBOX: case FormTemplateField::CHECKBOX:

                        $others = isset($answers[$field->id . '.others']) ? $answers[$field->id . '.others'] : '';


                        $vars['value'] = json_encode($value);
                        $vars['value_others'] = $others;

                    break;
                    case FormTemplateField::TABLE:

                        $vars['value'] = json_encode($value);

                    break;
                    default:

                        $vars['value'] = $value;

                    break;
                }


                /* Check if answer already exists */
                if($tempForm) {
                    $vars['temp_form_id'] = $tempForm->id;

                    $answer = $tempForm->answers()
                                    ->where('temp_form_id', $tempForm->id)
                                    ->where('form_id', $tempForm->form_id)
                                    ->where('form_template_field_id', $field->id)
                                    ->get();
                } else {
                    $answer = $this->answers()
                                    ->where('form_id', $this->id)
                                    ->where('form_template_field_id', $field->id)
                                    ->get();
                }

                if($answer->count() > 0) {

                    /* Update answer */
                    $answer = $answer->first();
                    $answer->update($vars);

                } else {

                    /* Create answer */
                    $answer = $tempForm ? TempFormAnswer::create($vars) : FormAnswer::create($vars);
                }
            }
        }
    }

    public function addApprovers($tempForm = null) {

        /* Check if form approvers exist already */
        if($tempForm) {
            if($tempForm->approvers()->count()) {

                /* Delete approvers */
                $tempForm->approvers()->delete();
            }
        } else {
            if($this->approvers()->count()) {

                /* Delete approvers */
                $this->approvers()->delete();
            }
        }


        /*
        | Loop each form template approvers
        |----------------------------------------------*/
        $hasManager = false;
        foreach($this->template->approvers as $key => $approver) {

            $currApprover = null;
            $sortID = $key;

            $isEnable = $tempForm ? ($tempForm->approvers()->count() > 0 ? false : true) :
                                    ($this->approvers()->count() > 0 ? false : true);

            /* Fetch the approver */
            switch($approver->type) {
                case FormTemplateApprover::LEVEL:

                    /* Check if there is already a manager */  
                    if(!$hasManager) {

                        /* Fetch approver by level */
                        switch($approver->type_value) {
                             
                            case FormTemplateApprover::IMMEDIATE_LEADER: 
                               
                                $currApprover = $this->employee->getImmediateLeader();   
                                   

                            break;
                            case FormTemplateApprover::NEXT_LEVEL_LEADER: 

                                $currApprover = $this->employee->getNextLevelLeader();         
                                    
                            break;                            
                        }
                       
                   

                        /* Check if approver is a manager */
                        if($currApprover) {
                            if($this->template->enableManagerial && $currApprover->isExecutiveUp()) {
                                $hasManager = true;                
                            }
                        }
                    }

                break;
                case FormTemplateApprover::EMPLOYEE:
                
                    $employee = $approver->employee;


                    /* Check if set employee is on vacation */
                    if($employee && $employee->isOnVacation())
                        $employee = $employee->proxy;
                    
                    $currApprover = $employee;

                break;
                case FormTemplateApprover::COMPANY:
                
                    /* Fetch approver by type */
                    switch($approver->type_value) {
                        case FormTemplateApprover::HR: 

                            $currApprover = $this->getCompanyHR();

                        break;
                        case FormTemplateApprover::OD:

                            $currApprover = $this->getCompanyOD();
                                
                        break;                            
                    }                        

                break;                    
            }


            /* Check if approver exists */
            if($currApprover) {

                /* Create the form approver */
                $this->createApprover($approver->id, $currApprover->id, $approver->type, $approver->type_value, $sortID, $isEnable, false, $tempForm);
            }
        }

   
        /*
        | If form is L&D
        |----------------------------------------------*/
        switch($this->template->category->id) {  
            case FormTemplateCategory::LD:

                $settings = Settings::get()->first();
                $od = $this->getCompanyOD();
                $sort = $this->template->approvers()->count();


                /* Check and calculate total cost if > P100,000*/
                if((!$this->isLocal) || ($this->calculateTotalCost() > 100000)) {

                    /* check if ceo is setup, add as approver if yes */
                    if($settings->ceo)
                        $this->createApprover(null, $settings->ceo->id, FormTemplateApprover::CEO, 0, ($sort + 1), false, false, $tempForm);
                }

                /* check if OD is setup, add as approver if yes */
                if($od)
                    $this->createApprover(null, $od->id, FormTemplateApprover::COMPANY, FormTemplateApprover::OD, ($sort + 2), false, false, $tempForm);

            break;
        }


        /*
        | Check for request type
        |----------------------------------------------*/
        switch($this->template->request_type) {
            case FormTemplate::TRAVELORDER:
                
                $settings = Settings::get()->first();
                $sort = $this->template->approvers()->count();


                /* Check if its local */
                if(!$this->isLocal) {

                    /* Fetch group head, add as approver if yes */
                    $division = $this->employee->getDivision();
                    if($division) {

                        /* Check if group head exists */
                        if($division->group_head)
                            $this->createApprover(null, $division->group_head->id, FormTemplateApprover::GROUP_HEAD, 0, ($sort + 1), false, false, $tempForm);
                    }

                    /* check if ceo is setup, add as approver if yes */
                    if($settings->ceo)
                        $this->createApprover(null, $settings->ceo->id, FormTemplateApprover::CEO, 0, ($sort + 2), false, false, $tempForm);
                }

            break;
        }


        /* Check if there are no approvers */
        if(!$this->approvers()->count()) { 

            /* Update the form as approved */
            $this->setAsApproved(1);

            return true;
        }        
    }

    public function createApprover($formApproverID, $approverID, $type, $typeValue, $sort, $isEnable, $allowDuplicate = false, $tempForm = null) {

        $vars = [];
        $enable = $this->template->isInOrder() ? $isEnable : true;

        /* Set variable */
        $vars['form_id'] = $this->id;
        $vars['form_template_approver_id'] = $formApproverID;
        $vars['approver_id'] = $approverID;
        $vars['type'] = $type;
        $vars['type_value'] = $typeValue;
        $vars['sort'] = $sort;
        $vars['enabled'] = $enable;


        /* Check if approver is the requester */
        if($this->employee_id != $approverID || $allowDuplicate) {

            /* Check if approver exist */
            if($tempForm) {
                $vars['temp_form_id'] = $tempForm->id;
                
                $formApprover = $tempForm->approvers()->where('temp_form_id', $tempForm->id)
                                                ->where('form_id', $this->id)
                                                ->where('approver_id', $approverID)
                                                ->get();
            } else {
                $formApprover = $this->approvers()->where('form_id', $this->id)
                                                ->where('approver_id', $approverID)
                                                ->get();
            }
            
            
            /* Check if approver already exists */
            if($formApprover->count() > 0) {

                /* Set to pending again */
                $formApprover = $formApprover->first();
                $formApprover->setAsPending();

            } else {

                /* Create the form approver */
                $formApprover = $tempForm ? TempFormApprover::create($vars) : FormApprover::create($vars);
            }


            /* Notify approver */
            if($enable) {

                if($tempForm) {
                    $formApprover->approver->notify(new RequestHasUpdate($tempForm, $formApprover));
                } else {
                    $formApprover->approver->notify(new RequestHasApprover($this, $formApprover));
                }
            }
        }
    }

    public function createLog($user, $text, $link = null) {
        FormLog::create([
            'employee_id' => $user->id,
            'form_id' => $this->id,
            'text' => $text,
            'link' => $link,
        ]);
    }

    public function calculateTotalCost() {
        $total = 0;

        /* Get expenses */
        $total = round($this->course_cost + $this->accommodation_cost + $this->meal_cost + $this->transport_cost + $this->others_cost);

        /* Save it */
        $this->total_cost = $total;
        $this->save();
        
        return $total;
    }

    public function withdraw() {

        /* Set status to Cancelled */
        $this->setAsCancelled(1);

        /* Check if a ticket exist and cancel it */
        if($this->ticket)
            $this->ticket->setAsCancelled(1);
    }

    public function addAttachment($name, $path, $user) {

        /* Create the photo data */
        $attachment = FormAttachment::create([
            'form_id' => $this->id,
            'employee_id' => $user->id,
            
            'name' => $name,
            'path' => $path
        ]);

        /* Add attachment */
        $this->attachments()->save($attachment);
    }  

    public function addTempAttachment($temps) {

        foreach($temps as $key => $value) {
            $tempAttachment = TempAttachment::find($value);

            /* Check if temp exists */ 
            if($tempAttachment) {
                /* Create the photo data */
                $attachment = FormAttachment::create([
                    'form_id' => $this->id,
                    'employee_id' => $tempAttachment->employee->id,
                    
                    'name' => $tempAttachment->name,
                    'path' => $tempAttachment->path
                ]);

                /* Add attachment */
                $this->attachments()->save($attachment);
                /* Delete temp */
                $tempAttachment->delete();
            }
        }
    }


    public static function createRequest($request, $templateID, $formID = null, $returnForm = false)
    {
        
        $user = \Auth::user();

        $formTemplate = FormTemplate::findOrFail($templateID);


        /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();

        $form = $formID ? Form::findOrFail($formID) : null;
        $redirectURL = route('requests');

        $except = [
            'name', 'mrreservationtime', 'room_id', 'location_id',
            'save', 'draft', 'fields', 'event_id', 'hour', 'meridian', 'minute', 'attachments', 'color',
        ];

        $vars = $request->except($except);


        /* Check if form exists & already closed */
        if($form && $form->ticket && $form->ticket->isClosed()) {

            /* Double check if there are changes involved */
            if($form->hasChanges($request)) {
                $tempForm = $form->hasPendingUpdate();

                /* Create temp if non-existent */
                if(!$tempForm) {
                    $vars['form_id'] = $form->id;
                    $vars['form_template_id'] = $formTemplate->id;
                    $vars['requester_id'] = $user->id;

                    $tempForm = TempForm::create($vars);
                }

                /* Add answers */
                $tempForm->addAnswers($request->input('fields'));


                 /* Check form types for special var convertions */
                if($formTemplate->isMeetingRoom()) {

                    TempMrReservation::submit($request, null, true, false, $tempForm, true, $form);
                    
                }


                /* Add approvers */
                if($tempForm->addApprovers()) {


                    /*
                    | @End Transaction
                    |---------------------------------------------*/
                    \DB::commit();

                    return [
                        'response' => 1,
                        'title' => 'Update Request',
                        'message' => 'Successfully updated request'
                    ];

                } else {

                    /*
                    | @End Transaction
                    |---------------------------------------------*/
                    \DB::commit();

                    return [
                        'response' => 1,
                        'title' => 'Update Request',
                        'message' => 'Request update sent for approval'
                    ];
                }

            } else {                

                return [
                    'response' => 1,
                    'title' => 'Update Request',
                    'message' => 'No changes detected'
                ];
            }

        } else {

            /* Check if form exists */
            if($form) {

                /* Update Form */
                $form->update($vars);

            } else {

                $vars['form_template_id'] = $formTemplate->id;
                $vars['creator_id'] = $user->id;

                /* Create form */
                $form = Form::create($vars);
            }


            /* Check if save */
            if($request->has('save'))
                $form->setAsPending(1);

            /* Check if draft */
            if($request->has('draft'))
                $form->setAsDraft(1);        


            /* Create the answers */
            $form->addAnswers($request->input('fields'));

            /* Create the attachments */
            if($request->has('attachments')) {
                $form->addTempAttachment($request->input('attachments'));
            }


            /* Check if this is created by an assignee */
            if($form->employee->id == $user->id) { 

                /* Check if form template is for an event */
                switch($formTemplate->category->id) { 

                    case FormTemplateCategory::EVENT:

                        $event = Event::findOrFail($request->input('event_id'));

                        /* Create event participants */
                        $eventParticipant = $event->attend($form);


                        /* Create log */
                        $form->createLog($user, 'Submitted the request', $form->renderViewURL());

                        /*
                        | @End Transaction
                        |---------------------------------------------*/
                        \DB::commit();

                        return [
                            'response' => 1,
                            'redirectURL' => route('event.show', $event->id),
                            'title' => 'Attend event',
                            'message' => 'Successfully submitted form for ' . $event->title . ' event'
                        ];

                    break;
                    case FormTemplateCategory::LD:

                        /* Redirect to */
                        $redirectURL = route('learnings');

                    case FormTemplateCategory::FORM: default:

                        /* Set the approvers, if not save as draft */
                        if($request->has('save')) {
                            $form->addApprovers(); 

                            /* Create log */
                            $form->createLog($user, 'Submitted the request', $form->renderViewURL());
                        }

                        /*
                        | @End Transaction
                        |---------------------------------------------*/
                        \DB::commit();

                    break;
                }

            } else {

                /* Set the assignee */
                $form->assignee_id = $user->id;
                $form->save();

                /* Set the approvers, if not save as draft */
                if($request->has('save'))
                    $form->addApprovers();


                /* Create log */
                $form->createLog($user, 'Submitted the request for ' . $form->employee->renderFullname(), $form->renderViewURL());

                /*
                | @End Transaction
                |---------------------------------------------*/
                \DB::commit();            
            }

            if ($returnForm) {
                return $form;
            }
            $msg = 'Successfully submitted form ';
            $message = $msg . $form->template->name;

            if ($request->has('draft')){
                $msg = ' has been successfully saved as draft.';
                $message = $form->template->name . $msg;
            }
            

            return [
                'response' => 1,
                'redirectURL' => $redirectURL,
                'title' => 'New Request',
                'message' => $message,
                'form' => $form,
            ];
        }
    }


    /*
    |-----------------------------------------------
    | @Checker
    |-----------------------------------------------
    */
    public function canUpdate() {

        $user = \Auth::user();


        /* Check if super admin */
        if(!$user->isSuperUser() && !$user->hasRole('Creating/Designing/Editing/Removing of Forms')) {

            /* Fetch approver IDs */
            $approverIDs = $this->approvers->pluck('approver_id');
            /* Fetch technician ID */
            $technicianID = $this->ticket ? $this->ticket->technician->id : null;

            /* Fetch company technicians */
            $companyTechIDs = $this->getCompanyTechnicians()->pluck('id');


            /* Check if the user is the supervisor, or the technician */
            if($this->employee->supervisor_id != $user->id && 
                !$approverIDs->intersect([$user->id])->count() && 
                !$companyTechIDs->intersect([$user->id])->count() && 
                $technicianID != $user->id
            )
                return false;
        }

        return true;
    }

    public function canUpdateDetails() {
        $user = \Auth::user();


        /* Check if form is already is closed */
        /* add !$this->template->isMeetingRoom() to validate meeting room reservation, meeting room can be updated  */
        if($this->ticket && !$this->ticket->isClosed() && !$this->template->isMeetingRoom())
            return false;

        /* Check if owner */
        
        if($user->id != $this->employee_id && $user->id != $this->assignee_id && $user->id != $this->ticket->technician_id)
            return false;


        /* Check if HR */
        // !@TODO

        return true;
    }

    public function checkIfAllApprove() {
        return !$this->approvers()->where('status', FormApprover::PENDING)->exists();
    }

    public function hasPendingUpdate() {
        $updates = $this->form_updates()->where('status', FormApprover::PENDING)->get();

        if($updates->count() > 0)
            return $updates->first();

        return false;
    }

    public function hasChanges($request) {
        $answers = $request->input('fields');

        /* Loop each form template fields */
        foreach($this->template->fields as $key => $field) {
            $vars = [];

            /* Create the variable array */
            $vars['form_id'] = $this->id;
            $vars['form_template_field_id'] = $field->id;


            /* No need to create if the type is header & paragraph */
            if($field->type != FormTemplateField::HEADER && $field->type != FormTemplateField::PARAGRAPH) {

                $value = isset($answers[$field->id]) ? $answers[$field->id] : '';


                /* Set the answer value */
                switch($field->type) {
                    case FormTemplateField::RADIOBOX: case FormTemplateField::CHECKBOX:

                        $others = isset($answers[$field->id . '.others']) ? $answers[$field->id . '.others'] : '';


                        $vars['value'] = json_encode($value);
                        $vars['value_others'] = $others;

                    break;
                    case FormTemplateField::TABLE:

                        $vars['value'] = json_encode($value);

                    break;
                    default:

                        $vars['value'] = $value;

                    break;
                }


                /* Check if answer already exists */
                $answer = $this->answers()
                                ->where('form_id', $this->id)
                                ->where('form_template_field_id', $field->id)
                                ->get();

                if($answer->count() > 0) {

                    /* Update answer */
                    $answer = $answer->first();

                    if($answer->value != $vars['value'])
                        return true;
                }
            }
        }

        /* Check purpose */
        if($this->purpose || $request->input('purpose'))
            return true;

        /* Check L&D fields */
        switch($this->template->category->id) { 
            case FormTemplateCategory::LD:        
                if($this->course_cost != $request->course_cost || $this->accommodation_cost != $request->accommodation_cost || 
                    $this->meal_cost != $request->meal_cost || $this->transport_cost != $request->transport_cost || 
                    $this->others_cost != $request->others_cost || $this->total_cost != $request->total_cost)
                    return true;
            break;
        }

        /* Check Meeting Room fields */
        if($this->template->isMeetingRoom()) {
            
            if (
                $this->mr_reservation->location_id !== $request->location_id ||
                $this->mr_reservation->room_id !== $request->room_id ||
                $this->mr_reservation->name !== $request->name ||
                $this->mr_reservation->description !== $request->description ||
                $this->mr_reservation->color !== $request->color
            ) {
                return true;
            }

            foreach ($request->input('mrreservationtime') as $mrReservationTime) {

                if (
                    !$this->mr_reservation->mr_reservation_times()->where('date', $mrReservationTime['date'])
                                                                ->where('start_time', $mrReservationTime['start_time'])
                                                                ->where('end_time', $mrReservationTime['end_time'])->count()
                ) {
                    return true;
                }
            }

        }


        return false;
    }

    public function isApprover($approver) {

        return $this->approvers()->where('approver_id', $approver->id)->where('status', FormApprover::PENDING)->exists();
        
    }

    public function isWithdrawn() {
        return $this->status == Form::CANCELLED;
    }

    public function isDraft() {
        return $this->status == Form::DRAFT;
    }

    public function isPending() {
        return $this->status == Form::PENDING;
    }    

    public function isApproved() {
        return $this->status == Form::APPROVED;
    }    

    public function isDisapproved() {
        return $this->status == Form::DISAPPROVED;
    }

    public function isArchivable() {
        return $this->isWithdrawn() || $this->isDraft() || $this->isDisapproved();
    }    

    public function isEditable() {

        /* Check form status */
        if(!$this->trashed() || $this->isDraft() || $this->isDisapproved() || $this->isWithdrawn())
            return true;

        return false;
    }


    /*
    |-----------------------------------------------
    | @Render
    |-----------------------------------------------
    */
    public function renderAnswers($isHTML = false) {

        /* Fetch tags */
        $labelStartTags = $isHTML ? "<h3>" : "";
        $labelEndTags = $isHTML ? "</h3>" : "";
        $openingTags = $isHTML ? "<span>" : "";
        $closingTags = $isHTML ? "</span>" : "\n";


        $answers = $this->answers;
        $result = '';

        if($isHTML)
            $result .= "<div class='has-approver'>";



        /* Check for static fields depending on template type */
        /* !@REFACTOR: Currently other template type is checked after the main answers */
        switch ($this->template->request_type) {
            case FormTemplate::MEETINGROOM:

                $mrReservation = $this->mr_reservation;

                if ($mrReservation) {
                    $result .= $labelStartTags . 'Title: ' . $labelEndTags . $openingTags . $mrReservation->name . $closingTags;

                    foreach ($mrReservation->mr_reservation_times as $index => $mrReservationTime) {

                        if (count($mrReservation->mr_reservation_times) > 1) {
                            $result .= $labelStartTags . 'Day: ' . $labelEndTags . $openingTags . ($index + 1) . $closingTags;
                        }

                        if($mrReservationTime->date) {
                            $result .= $labelStartTags . 'Date: ' . $labelEndTags . $openingTags . $mrReservationTime->date . $closingTags;
                        }

                        if($mrReservationTime->start_time) {
                            $result .= $labelStartTags . 'Start Time: ' . $labelEndTags . $openingTags . $mrReservationTime->start_time . $closingTags;
                        }

                        if($mrReservationTime->end_time) {
                            $result .= $labelStartTags . 'End Time: ' . $labelEndTags . $openingTags . $mrReservationTime->end_time . $closingTags;
                        }
                    }
                }

            break;
        }


        /* Loop each answers */
        foreach($this->template->fields as $key => $field) {
            /* Check if answer exists */
            foreach($answers as $answer) {

                if($answer->form_template_field_id == $field->id && $field->type != FormTemplateField::HEADER && $field->type != FormTemplateField::PARAGRAPH) {

                    /* Get only the textfield & datefield */
                    switch($field->type) {
                        case FormTemplateField::RADIOBOX:

                            /* Check if the fields has been changed */
                            $result .= $labelStartTags . ($field->id == $answer->form_template_field_id ? $field->label : "'Form field has been changed'" ) . " : " . $labelEndTags;                                                        
                        
                            /* Check if answer value exist */
                            if($value = json_decode($answer->value)) {

                                $ans = FormTemplateOption::find($value[0]);

                                /* Get the answer */
                                $result .= $openingTags . ($ans ? $ans->value : "") . $closingTags;
                            } else {
                                $result .= "";
                            }

                        break;
                        case FormTemplateField::CHECKBOX:

                            /* Check if the fields has been changed */
                            $result .= $labelStartTags . ($field->id == $answer->form_template_field_id ? $field->label : "'Form field has been changed'" ) . " : " . $labelEndTags;                                                        
                    
                            /* Fetch toggled answer */
                            $jsonAnswer = json_decode($answer->value);
                            if(is_array($jsonAnswer)) {
                                foreach($jsonAnswer as $value) {
                                    foreach($field->options as $key => $option) {
                                        $result .= (($value == $option->id) ? $openingTags . $option->value . ", " . $closingTags : "");
                                    }
                                }
                            }

                            $result .= $closingTags;

                        break;
                        case FormTemplateField::TABLE:

                            $noChange = $field->id == $answer->form_template_field_id;

                            /* Check if the fields has been changed */
                            $result .= $labelStartTags . ($noChange ? $field->label : "'Form field has been changed'" ) . $labelEndTags;

                            if($isHTML) {

                                /* Proceed only if table hasn't been changed yet */
                                if($noChange) {

                                    /* Create table header */
                                    $result .= "<table class='table border'>";

                                    $result .= "<thead><tr>";
                                    foreach($field->options as $option) {
                                        $result .= "<th>" . $option->value . "</th>";
                                    }
                                    $result .= "</tr></thead>";

                                    $result .= "<tbody>";
                                    /* Create table data */
                                    $jsonAnswer = json_decode($answer->value);
                                    if(is_array($jsonAnswer)) {
                                        foreach($jsonAnswer as $value) {
                                            $result .= "<tr>";
                                            foreach($field->options as $option) {
                                                $result .= "<td>" . $value->{$option->id} . "</td>";
                                            }
                                            $result .= "</tr>";
                                        }
                                    }
                                    $result .= "</tbody>";

                                    $result .= "</table>";
                                }
                            } else {

                                if($noChange) {

                                    $result .= "\n";

                                    /* Create table data */
                                    $jsonAnswer = json_decode($answer->value);
                                    if(is_array($jsonAnswer)) {
                                        foreach($jsonAnswer as $value) {
                                            foreach($field->options as $option) {
                                                $result .= $labelStartTags . $option->value . " : " . $labelEndTags;

                                                if(isset($value->{$option->id}))
                                                    $result .= $openingTags . $value->{$option->id} . $closingTags;
                                            }
                                        }
                                    }                                  
                                }
                            }

                        break;
                        case FormTemplateField::TEXTFIELD: case FormTemplateField::TEXTAREA:
                        case FormTemplateField::DATEFIELD: case FormTemplateField::TIME:
                        case FormTemplateField::DROPDOWN:
                            
                            /* Check if the fields has been changed */
                            $result .= $labelStartTags . ($field->id == $answer->form_template_field_id ? $field->label : "'Form field has been changed'" ) . " : " . $labelEndTags;

                            /* Get the answer */
                            $result .= $openingTags . $answer->value . $closingTags;

                        break;
                    }
                }
            }
        }


        /* Check for static fields depending on template type */
        /* !@REFACTOR: Currently other template type is checked before the main answers */        
        if($this->template->category->forLearning()) {
            $result .= $labelStartTags . 'Training Venue : ' . $labelEndTags . $openingTags . $this->isLocal . $closingTags;
            $result .= $labelStartTags . 'Course Cost : ' . $labelEndTags . $openingTags . $this->course_cost . $closingTags;
            $result .= $labelStartTags . 'Accomodation Cost : ' . $labelEndTags . $openingTags . $this->accommodation_cost . $closingTags;
            $result .= $labelStartTags . 'Meal Cost : ' . $labelEndTags . $openingTags . $this->meal_cost . $closingTags;
            $result .= $labelStartTags . 'Transport Cost : ' . $labelEndTags . $openingTags . $this->transport_cost . $closingTags;
            $result .= $labelStartTags . 'Others Cost : ' . $labelEndTags . $openingTags . $this->others_cost . $closingTags;
            $result .= $labelStartTags . 'Total Cost : ' . $labelEndTags . $openingTags . $this->total_cost . $closingTags;
        }


        if($isHTML)
            $result .= "</div>";        
        
        return $result;
    }

    public function renderUpdates() {
        $details = '';
        $lineBreak = "\n";

        if ($this->ticket) {
            foreach ($this->ticket->updates as $index => $update) {
                $details .= 'Update #' . ($index + 1) . $lineBreak;
                $details .=  $update->employee ? 'Employee: ' . $update->employee->renderFullname() . $lineBreak : '';
                $details .= 'Description: ' .$update->description;
            }
        }

        return $details;
    }

    public static function renderTableFilter() {
        $array = [];

        /* Add in status options */
        $array[0] = [
            'label' => 'status',
            'options' => [],
        ];

        foreach (Form::getStatus() as $key => $value) {
            array_push($array[0]['options'], [
                'id' => $value['value'],
                'label' => $value['label'],
            ]);
        }


        return $array;
    }

    public function renderApprover($tempForm = null) {
        $approverStr = '';
        $approvers = $tempForm ? $tempForm->approvers : $this->approvers;

        /* Loop through the approver */
        foreach($approvers as $key => $approver) {
            $approverStr .= $approver->approver->renderFullname() . " \n";

            if($approver->reason)
                $approverStr .= $approver->reason . " \n\n";
        }

        return $approverStr;
    }

    public function renderStatus() {
        switch ($this->template->category->id) {
            case FormTemplateCategory::EVENT: 
                return $this->renderConstantLabel(Form::getStatus(), $this->status);

            case FormTemplateCategory::LD: case FormTemplateCategory::FORM: 
                return ($this->isApproved() && $this->ticket) ? $this->ticket->renderStatus() : $this->renderConstantLabel(Form::getStatus(), $this->status);
        }
    }

    public function renderSubmitStatus() {
        $message = 'Submitted';

        if ($this->isResubmitting) {
            $message = 'Resubmitted';
        }

        return $message;
    }

    public function renderTotalCost() {
        return 'P' . number_format($this->total_cost, 2);
    }

    public function renderTicket() {
    
        if($this->template->category->forLearning())
            return 'N/A';

        return $this->ticket ? $this->ticket->id : '';
    }

    public function renderConstantLabel($array, $value) {
        $result = $this->renderConstants($array, $value);

        if($result)
            return $result['label'];
    }

    public function renderConstants($array, $value) {

        /* Loop through the array */
        foreach ($array as $obj) {
            
            if($obj['value'] == $value)
                return $obj;
        }
    }

    public function renderFetchLogsURL() {
        return route('requestlog.fetch', $this->id);
    }

    public function renderDetailsURL() {
        return route('request.fetchanswers', $this->id);
    }

    public function renderViewURL() {
        return route('request.show', $this->id); 
    }
    public function isCurrentApprover($user = null) {
        $approver = $this->approvers()->where('status', FormApprover::PENDING)->orderBy('sort')->first();
        $action = false;

        if ($approver && $user) {
            if ($approver->approver_id === $user->id) {
                $action = true;
            }
        }

        return $action;
    }
}
