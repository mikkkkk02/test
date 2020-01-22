<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Notifications\Tickets\TicketWasUpdated;
use App\Notifications\Tickets\TicketWasClosed;
use App\Notifications\Tickets\TicketWasOnHold;
use App\Notifications\Tickets\TicketWasCancelled;
use App\Notifications\Tickets\TicketHasTechnician;

use Laravel\Scout\Searchable;
use Carbon\Carbon;
use App\Helper\RoleChecker;

use App\FormTemplate;
use App\FormTemplateCategory;
use App\TicketAttachment;

class Ticket extends Model
{
    use Searchable;

    public $asYouType = true;

    /**
     * Response Title and Message
     * @var string
     */
    public $response_message;
    public $response_title;

    /*
    |-----------------------------------------------
    | @Columns
    |-----------------------------------------------
    */
    const MINIMAL_COLUMNS = [
        'id', 'form_id', 'employee_id', 'technician_id',
        'priority', 'state', 'status',
        'start_date', 'date_closed',
        'created_at', 'updated_at',
    ];
    const TABLE_COLUMNS = [
        'id', 'form_id', 'employee_id', 'technician_id',
        'priority', 'state', 'status',
        'start_date', 'date_closed',
        'created_at', 'updated_at',        
    ];
    const JOIN_COLUMNS = [
        'tickets.id', 'tickets.form_id', 'tickets.employee_id', 'tickets.technician_id',
        'tickets.priority', 'tickets.state', 'tickets.status',
        'tickets.start_date', 'tickets.date_closed',
        'tickets.created_at', 'tickets.updated_at',        
    ];    

    /*
    |-----------------------------------------------
    | @Status
    |-----------------------------------------------
    */
    const OPEN = 0;
    const ONHOLD = 1;
    const CLOSE = 2;
    const CANCELLED = 3;

    /*
    |-----------------------------------------------
    | @State
    |-----------------------------------------------
    */
    const ONGOING = 0;
    const ONTIME = 1;
    const DELAYED = 2;

    /*
    |-----------------------------------------------
    | @Priority
    |-----------------------------------------------
    */
    const LOW = 0;
    const MEDIUM = 1;
    const HIGH = 2;

    protected $guarded = [];
    protected $appends = ['extra'];


    public function priority() {
    	return $this->belongsTo(Priority::class);
    }

    public function form() {
    	return $this->belongsTo(Form::class)->withTrashed();
    }

    public function owner() {
    	return $this->belongsTo(User::class, 'employee_id')->withTrashed();
    }

    public function technician() {
    	return $this->belongsTo(User::class, 'technician_id')->withTrashed();
    }

    public function attachments() {
        return $this->hasMany(TicketAttachment::class);
    }

    public function travel_order_details() {
        return $this->hasMany(TicketTravelOrderDetail::class, 'ticket_id');
    }

    public function updates() {
        return $this->hasMany(TicketUpdate::class);
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
            'form' => $this->form->template->name,
            'priority' => $this->renderPriority(),
            'category' => $this->form->template->category->name,
            'requested_by' => $this->owner->renderFullname(),
            'assigned_to' => $this->technician ? $this->technician->renderFullname() : '',
            'SLA' => $this->form->template->sla,
            'status' => $this->renderStatus(),
            'state' => $this->renderState(),
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
            'state' => $this->renderState(),
            'priority' => $this->renderPriority(),
            'view' => $this->renderViewURL(),
        ];
    }

    public function setAsOpen($save = false) {
        $this->status = Ticket::OPEN;
        $this->date_closed = null;

        if($save)
            $this->save();
    }

    public function setAsClose($save = false) {
        $this->status = Ticket::CLOSE;
        $this->date_closed = Carbon::now();

        if($save)
            $this->save();
    }    

    public function setAsOnHold($save = false) {
        $this->status = Ticket::ONHOLD;

        if($save)
            $this->save();
    }    

    public function setAsCancelled($save = false) {
        $this->status = Ticket::CANCELLED;
        $this->date_closed = Carbon::now();

        if($save)
            $this->save();
    } 

    public function setAsOnGoing($save = false) {
        $this->state = Ticket::ONGOING;

        if($save)
            $this->save();
    }    
    
    public function setAsOnTime($save = false) {
        $this->state = Ticket::ONTIME;

        if($save)
            $this->save();
    }    
    
    public function setAsDelayed($save = false) {
        $this->state = Ticket::DELAYED;

        if($save)
            $this->save();
    }

    public static function getStatus() {
        return [
            ['label' => 'OPEN', 'class' => 'bg-primary', 'color' => '#13b3f5', 'value' => Ticket::OPEN],
            ['label' => 'ON HOLD', 'class' => 'bg-yellow', 'color' => '#fae021', 'value' => Ticket::ONHOLD],
            ['label' => 'CLOSED', 'class' => 'bg-green', 'color' => '#3ce96b', 'value' => Ticket::CLOSE],
            ['label' => 'CANCELLED', 'class' => 'bg-red', 'color' => '#ff9a9f', 'value' => Ticket::CANCELLED],
        ];
    }

    public static function getStates() {
        return [
            ['label' => 'Ongoing', 'class' => 'text-yellow', 'value' => Ticket::ONGOING, 'image' => '/image/charts/chart2.png'],
            ['label' => 'On-time', 'class' => 'text-white', 'value' => Ticket::ONTIME, 'image' => '/image/charts/chart3.png'],
            ['label' => 'Delayed',  'class' => 'text-red', 'value' => Ticket::DELAYED, 'image' => '/image/charts/chart4.png'],
        ];
    }

    public static function getPriorities() {
        return [
            ['label' => 'Low', 'class' => 'bg-yellow', 'value' => Ticket::LOW],
            ['label' => 'Medium', 'class' => 'bg-orange', 'value' => Ticket::MEDIUM],
            ['label' => 'High', 'class' => 'bg-red', 'value' => Ticket::HIGH],
        ];
    } 


    public static function getAllTicket($start, $end, $status){
        $datenow = date('Y-m-d');
        $columns = Ticket::JOIN_COLUMNS;
        $all = \DB::table('tickets as t')
        ->select(
            't.id as ticketId',
            't.form_id as formId',
            'f.formTempName as form',
            \DB::raw('(case 
                WHEN t.priority = "0" then "LOW" 
                WHEN t.priority = "1" then "MEDIUM"
                WHEN t.priority = "2" then "HIGH"
            end) priority'),
            'f.formTempcategoryName as category',
            \DB::raw('CONCAT(emp.last_name, " ", emp.first_name) AS requestedBy'),
            \DB::raw('CONCAT(tech.last_name, " ", tech.first_name) AS assignedTo'),
            \DB::raw('CONCAT(f.formTempSla, " Day(s)") as sla'),
            \DB::raw('DATE_ADD(t.start_date, INTERVAL f.formTempSla DAY) as date_needed'),
            't.date_closed as date_closed',
            't.created_at as date_created',
            \DB::raw('(case 
                WHEN t.status= "0" then "OPEN" 
                WHEN t.status = "1" then "ON HOLD"
                WHEN t.status = "2" then "CLOSED"
                WHEN t.status = "3" then "CANCELLED"
            end) status'),
            't.state as state')
        ->where(function($query) use ($start, $end){
            if($start != null && $end != null){
                $query->where('t.start_date', '>=', $start);
                $query->where('t.start_date', '<=', $end);
            }
        })
        ->where(function($query) use ($status){
            if($status == 1){
                $query->whereIn('t.status', [0, 1]);
                // $query->where('t.status', 1);
            }
            elseif($status == 2){
                $query->where('t.status', 2);
            }
            elseif($status == 3){
                $query->where('t.status', 3);
            }
        })
        
        ->leftJoin(
            \DB::raw(
                '(SELECT 
                ft.id as formTempID,
                ft.form_template_category_id as formTempcategory_id,
                ft.travel_order_table_id as formTempTravelOrderTableId,
                ft.enableAttachment as formTempEnableAttachment,
                ft.enableManagerial as formTempEnableManagerial ,
                ft.type as formTemptype,
                ft.request_type as formTemprequest_type ,
                ft.approval_option as formTempApproval_option,
                ft.`name` as formTempName,
                ft.sla as formTempSla,
                forms.id as formID,
                forms.form_template_id as formFormTempId,
                forms.ticket_id as formsTicketId,
                forms.employee_id as formsEmpID,
                forms.assignee_id as formsAssignID,
                forms.deleted_at as deleted,
                ftc.id as formTempcategoryID,
                ftc.`name` as formTempcategoryName
                from form_templates AS ft
                    LEFT JOIN forms ON forms.form_template_id = ft.id
                    LEFT JOIN form_template_categories as ftc ON ftc.id = ft.form_template_category_id
                
                )as f  
                '), function($join)
                    {
                        $join->on('f.formID', '=', 't.form_id');
                    })  
        ->leftJoin('users as emp', 'emp.id', '=', 't.employee_id')
        ->leftJoin('users as tech', 'tech.id', '=', 't.technician_id')
        // ->where('f.deleted', NULL)
        // ->where(function($query) use ($date){
        //     if($date){
        //         $query->whereDate('t.created_at', $date);
        //     }
           
        // })
        // ->where('t.priority', 'LOW')
        // ->whereDate('t.created_at', $date)
        // ->take(20)
        ->orderBy('t.created_at','desc')
        ->get()->toArray();
        $array = array();
        foreach($all as $l){
            
            $date_closed = $l->date_closed ?  date('M j, Y g:i a', strtotime($l->date_closed)) : '';
            $current_date = date('M j, Y g:i a');
            $state = '';
            $deadline = $l->date_needed;
            // print_r($current_date.'<br>'. $deadline);die();
            if($l->status !== 'CANCELLED'){
                if($l->state == 0){
                    if( $current_date > $deadline ) {
                        $state = 'Delayed';
                    }else{
                        $state = 'On-time';
                    }
                }
                elseif($l->state == 1){
                    $state = 'On-time';
                }elseif($l->state == 2){
                    $state = 'Delayed';
                }  
            }else{
                $state = '';
            }
           
            
            array_push($array, array(
                $l->ticketId, 
                $l->formId, 
                $l->form,
                $l->priority,
                $l->category,
                $l->requestedBy,
                $l->assignedTo,
                $l->sla,
                date('M j, Y g:i a', strtotime($l->date_needed)),
                $date_closed,
                date('M j, Y g:i a', strtotime($l->date_created)) ,
                $l->status,
                $state
                )
            );
            
        }

        return $array ; 
    }       

      


    /*
    |-----------------------------------------------    
    | @Methods
    |-----------------------------------------------
    */
    public function updateStatus($status, $update) {
        /* Notify the user for the update */
        $this->owner->notify(new TicketWasUpdated($this, $update));

        /* added withTrashed() to get user deleted */
        $user = User::where('id', '=', $this->form->creator_id)->withTrashed()->first();

        /* Check & update the status */  
        if($this->status != $status) {
            switch($status) {
                case Ticket::OPEN:
                    
                    $this->setAsOpen(1);

                break;
                case Ticket::CLOSE:
                    
                    $this->setAsClose(1);

                    /* Update state */
                    $current = Carbon::now();
                    $this->updateState($current);
                    
                    /* Notify the user & immediate leader */
                    if($user->id == $this->owner->id) {
                        $this->owner->notify(new TicketWasClosed($this, true, $update));
                    } else {
                        $this->owner->notify(new TicketWasClosed($this, true, $update));
                        $user->notify(new TicketWasClosed($this, true, $update));
                    }
                        // $this->owner->supervisor->notify(new TicketWasClosed($this, false, $update));

                break;
                case Ticket::ONHOLD:

                    $this->setAsOnHold(1);

                    /* Notify the user & immediate leader */
                    if($user->id == $this->owner->id) {
                        $this->owner->notify(new TicketWasOnHold($this, $update));
                    } else {
                        $this->owner->notify(new TicketWasOnHold($this, $update));
                        $user->notify(new TicketWasOnHold($this, $update));
                    }

                break;
                case Ticket::CANCELLED:
                    
                    $this->setAsCancelled(1);

                    /* Notify the user & immediate leader */
                    if($user->id == $this->owner->id) {
                        $this->owner->notify(new TicketWasCancelled($this, $update));
                    } else {
                        $this->owner->notify(new TicketWasCancelled($this, $update));
                        $user->notify(new TicketWasCancelled($this, $update));
                    }
                    
                break;
            }
        }
    }

    public function updateState($date, $save = true) {
        $state = 'On-going';

        if($date->lte($this->renderDeadline())) {
            $state = 'On-time';
            $this->setAsOnTime($save);
        } else {
            $state = 'Delayed';
            $this->setAsDelayed($save);
        }

        return $state;
    }

    public function updateSLA($date) {
        $this->start_date = $date;
        $this->save();
    }

    public function assignTechnician($technician) {
        $this->technician()->associate($technician);
        $this->save();
        $user = User::where('id', '=', $this->form->creator_id)->get()->first();
        /* Notify user & technician */
        if($user->id == $this->owner->id) {
            $this->owner->notify(new TicketHasTechnician($this, true));
            $this->technician->notify(new TicketHasTechnician($this, false));
        } else {
            $user->notify(new TicketHasTechnician($this, true));
            $this->owner->notify(new TicketHasTechnician($this, true));
            $this->technician->notify(new TicketHasTechnician($this, false));
        }
    }

    public function addAttachment($name, $path, $user) {

        /* Create the photo data */
        $attachment = TicketAttachment::create([
            'ticket_id' => $this->id,
            'employee_id' => $user->id,
            
            'name' => $name,
            'path' => $path
        ]);

        /* Add attachment */
        $this->attachments()->save($attachment);
    }

    /*
    |-----------------------------------------------
    | @Checker
    |-----------------------------------------------
    */   
    public function canUpdate() {

        $user = \Auth::user();


        /* Check if super admin */
        if(!$user->isSuperUser()) {

            $companyTech = $this->form->getCompanyTechnicians();
            $companyTechIDs = $companyTech ? $companyTech->pluck('id') : collect([]);

            /* Or the technician */
            if($user->id != $this->technician_id && !$companyTechIDs->intersect([$user->id])->count()) {        

                $roleChecker = new RoleChecker();

                /* Check if user has permission */
                if(!$roleChecker->isCompanyAdmin('Editing/Removing of Tickets', $this))
                    return false;
            }
        }

        return true;
    }

    public function canUpdateDetails() {
        //!@TODO

        return false;
    } 

    public function canAttach() {
        $self = \Auth::user();
        
        return $self->id == $this->technician_id || $self->id == $this->employee_id || $self->isSuperUser();
    }

    public function canResubmit() {
        $self = \Auth::user();

        return $this->form->isApproved() && $this->isClosed() && ($self->id == $this->form->employee_id);
    }    

    public function isWithdrawable() {
        $self = \Auth::user();

        return !$this->form->isDisapproved() && !$this->form->isWithdrawn() && ($self->id == $this->form->employee_id || $self->id == $this->form->assignee_id);
    }

    public function isOpen() {
        return $this->status == Ticket::OPEN;
    }

    public function isClosed() {
        return $this->status == Ticket::CLOSE;
    }    

    public function isOnHold() {
        return $this->status == Ticket::ONHOLD;
    }

    public function isCancelled() {
        return $this->status == Ticket::CANCELLED;
    }

    public function isTravelOrder() {
        switch ($this->form->template->request_type) {
            case FormTemplate::TRAVELORDER:
                return $this->form->template->travel_order_table_id;
            default:
                return false;
        }
    }

    public function getTravelOrderTableRowCount() {
        $count = 0;
        $table = $this->form->template->travel_order_table;
        
        if ($table) {
            $count = count(json_decode($table->answers()->where('form_id', $this->form->id)->first()->value));
        }

        return $count;
    }

    public function isMeetingRoom() {
        switch ($this->form->template->request_type) {
            case FormTemplate::MEETINGROOM:
                return true;
            default:
                return false;
        }
    }

    public function isTechnician() {
        $user = \Auth::user();

        if(!$user->isSuperUser()) {

            $companyTech = $this->form->getCompanyTechnicians();
            $companyTechIDs = $companyTech ? $companyTech->pluck('id') : collect([]);

            /* Or the technician */
            if($user->id != $this->technician_id && !$companyTechIDs->intersect([$user->id])->count()) {        

                return false;
            }

        }

        return true;
    }

    public function isOwner() {
        $user = \Auth::user();

        if(!$user->isSuperUser()) {

            if($user->id == $this->employee_id && $user->id == $this->form->assignee_id) {
                return false;
            }
        }

        return true;
    }    

    /*
    |-----------------------------------------------
    | @Render
    |-----------------------------------------------
    */
    public static function renderTableFilter() {
        $array = [];

        /* Add in priorities filters */
        array_push($array, Ticket::renderConstantTable(Ticket::getPriorities(), 'priority'));

        /* Add in state filters */
        array_push($array, Ticket::renderConstantTable(Ticket::getStates(), 'state'));

        /* Add in status filters */
        array_push($array, Ticket::renderConstantTable(Ticket::getStatus(), 'status'));

        /* Add in categories filters */
        array_push($array, Ticket::renderConstantTable(FormTemplateCategory::getTypes(), 'category'));

        /* Add in form filters */
        array_push($array, Ticket::renderConstantTable(FormTemplate::getFilter(), 'form'));


        return $array;
    }

    public static function renderPriorityFilter() {
        return [Ticket::renderConstantTable(Ticket::getPriorities(), 'priority')];
    }

    public static function renderStateFilter() {
        return [Ticket::renderConstantTable(Ticket::getStates(), 'state')];
    }    

    public function renderStatus() {
        return $this->renderConstantLabel(Ticket::getStatus(), $this->status);
    }  

    public function renderStatusClass() {
        return $this->renderConstantClass(Ticket::getStatus(), $this->status);
    } 

    public function renderState() {
        $label = $this->renderConstantLabel(Ticket::getStates(), $this->state);
        return $label;
    }

    public function renderComputedState() {
        switch ($this->state) {
            case Ticket::ONTIME:
                    $label = 'On-time';
                break;
            case Ticket::ONGOING:
                    $current = Carbon::now();

                    if ($current->lte($this->renderDeadline())) {
                        $label = 'On-time';
                    } else {
                        $label = 'Delayed';
                    }

                break;
            case Ticket::DELAYED:
                    $label = 'Delayed';
                break;
            default:
                    $label = 'Ongoing';
                break;
        }

        return $label;
    }

    public function renderStateClass() {
        return $this->renderConstantClass(Ticket::getStates(), $this->state);
    } 

    public function renderPriority() {
        return $this->renderConstantLabel(Ticket::getPriorities(), $this->priority);
    }

    public function renderPriorityClass() {
        return $this->renderConstantClass(Ticket::getPriorities(), $this->priority);
    }

    public function renderDeadline() {
        $slaStart = Carbon::parse($this->start_date);
        $slaDeadline = Carbon::instance($slaStart)->addDays($this->form->template->sla);

        return $slaDeadline;
    }

    public function renderSubmitStatus() {
        return $this->form->renderSubmitStatus();
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

    public function renderViewURL() {
        return route('ticket.show', $this->id);
    }
}