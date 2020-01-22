<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;

use App\TicketUpdate;

class TempTicketUpdate extends Model
{
    /*
    |-----------------------------------------------
    | @Columns
    |-----------------------------------------------
    */
    const MINIMAL_COLUMNS = [
        'id', 'ticket_id', 'employee_id', 'approver_id',
        'reason', 'status',
        'created_at', 'approved_date',
    ];

    const TABLE_COLUMNS = [
        'id', 'ticket_id', 'employee_id', 'approver_id',
        'reason', 'description', 'status', 
        'created_at', 'approved_date',
    ];

    /*
    |-----------------------------------------------
    | @Status
    |-----------------------------------------------
    */
    const PENDING = 0;
    const APPROVED = 1;
    const DISAPPROVED = 2;

    protected $guarded = [];
    protected $appends = ['extra'];


    public function ticket() {
    	return $this->belongsTo(Ticket::class);
    }

    public function employee() {
        return $this->belongsTo(User::class, 'employee_id')->withTrashed();
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
            'status' => $this->renderStatus(),
            'view' => $this->renderViewURL(),
        ];
    }

    public static function getStatus() {
        return [
            ['label' => 'Pending', 'class' => 'bg-yellow', 'value' => TempTicketUpdate::PENDING],
            ['label' => 'Approved', 'class' => 'bg-green', 'value' => TempTicketUpdate::APPROVED],
            ['label' => 'Disapproved',  'class' => 'bg-red', 'value' => TempTicketUpdate::DISAPPROVED],
        ];
    }    


    /*
    |-----------------------------------------------    
    | @Methods
    |-----------------------------------------------
    */
    public function setAsApprove() {
        $this->approver_id = \Auth::user()->id;
        $this->approved_date = Carbon::now();
        $this->status = TempTicketUpdate::APPROVED;
        
        $this->save();
    }

    public function setAsDisapprove() {
        $this->approver_id = \Auth::user()->id;
        $this->approved_date = Carbon::now();
        $this->status = TempTicketUpdate::DISAPPROVED;
        
        $this->save();
    }    


    /*
    |-----------------------------------------------
    | @Checker
    |-----------------------------------------------
    */
    public function canApprove($user) {
        $companyTech = $this->ticket->form->getCompanyTechnicians();
        $companyTechIDs = $companyTech ? $companyTech->pluck('id') : collect([]);

        $roleChecker = new RoleChecker();


        /* Check if user has permission */
        if($roleChecker->isCompanyAdmin('Editing/Removing of Tickets', $this->ticket) || $roleChecker->isCompanyAdmin('Updating of Ticket Status', $this->ticket) ||
            $roleChecker->isCompanyAdmin('Generating of Ticketing Reports', $this->ticket))
            return true;

        return false;
    }


    /*
    |-----------------------------------------------
    | @Render
    |-----------------------------------------------
    */
    public function renderStatus() {
        return $this->renderConstantLabel(TempTicketUpdate::getStatus(), $this->status);
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

    public function renderViewURL() {
        return route('tempticketupdate.show', $this->id);
    }  
}
