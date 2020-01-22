<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Notification;

use App\Notifications\Learnings\IDPHasApprover;
use App\Notifications\Learnings\IDPTempWasApproved;
use App\Notifications\Learnings\IDPTempWasDisapproved;
use App\Notifications\Learnings\IDPWasUpdated;

use Laravel\Scout\Searchable;

use Carbon\Carbon;

use App\Idp;
use App\TempIdp;
use App\IdpApprover;
use App\Role;
use App\Settings;

class Idp extends Model
{
	use Searchable;

	public $asYouType = true;

    /*
    |-----------------------------------------------
    | @Columns
    |-----------------------------------------------
    */
    const TABLE_COLUMNS = [
        'id', 'employee_id', 'competency_id',
        'learning_type', 'competency_type', 'required_proficiency_level', 'current_proficiency_level', 'type',
        'details', 'completion_year',
        'status', 'approval_status',
    ];

    /*
    |-----------------------------------------------
    | @General Constant
    |-----------------------------------------------
    */
    const NONE = 0; 

    /*
    |-----------------------------------------------
    | @Learning Activity Type
    |-----------------------------------------------
    */
	const EDUCATION = 1;
	const EXPERIENCE = 2;
	const EXPOSURE = 3;

    /*
    |-----------------------------------------------
    | @Competency Type
    |-----------------------------------------------
    */
	const TECHNICAL = 0;
	const VALUES = 1;
	const LEADERSHIP = 2;

    /*
    |-----------------------------------------------
    | @Proficiency Level
    |-----------------------------------------------
    */
    const MINPROFICIENCY = 0;
    const MAXPROFICIENCY = 5;

    /*
    |-----------------------------------------------
    | @Type
    |-----------------------------------------------
    */  
	const WITHGAP = 1;
	const CONTLEARNING = 2;	
	const ADDCOMPETENCY = 3;

    /*
    |-----------------------------------------------
    | @Status
    |-----------------------------------------------
    */
	const ONGOING = 1;
	const FORCOMPLETION = 2;
    const COMPLETED = 3;

    /*
    |-----------------------------------------------
    | @Approval Status
    |-----------------------------------------------
    */
    const PENDING = 0;
    const APPROVED = 1; 
    const DISAPPROVED = 2;
    const DRAFT = 3;
    const CANCELLED = 4;


    protected $guarded = [];    
    protected $appends = ['extra'];    


    public function employee() {
        return $this->belongsTo(User::class, 'employee_id')->withTrashed();
    }

    public function competency() {
        return $this->belongsTo(IdpCompetency::class, 'competency_id');
    }

    public function temp() {
        return $this->hasOne(TempIdp::class, 'idp_id');
    }

    public function approvers() {
        return $this->hasMany(IdpApprover::class);
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
            'employee' => $this->employee->renderFullname(),
            'learning_activity_type' => $this->renderLearningActivityType(),
            'competency_type' => $this->renderCompetencyType(),
            'required_proficiency_level' => $this->required_proficiency_level,
            'current_proficiency_level' => $this->current_proficiency_level,
            'status' => $this->renderStatus(),
            'approval_status' => $this->renderApprovalStatus(),
            'type' => $this->renderType(),
			'completion_year' => $this->completion_year,            
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
            'learning' => $this->renderLearningActivityType(),
            'competency' => $this->renderCompetencyType(),
            'type' => $this->renderType(),
            'status' => $this->renderStatus(),
            'approvalview' => $this->renderApprovalViewURL(),
            'view' => $this->renderViewURL(),
            'approval_status' => $this->renderApprovalStatus(),
            'approval_class' => $this->renderApprovalClass(),
        ];
    }

    public function setAsApproved($save = false) {

        /* Check if for new request or update */
        if($this->isApproved()) {

            /* Save the temp to the original */
            $this->saveTemp();

        } else {

            $this->approval_status = Idp::APPROVED;


            /* Set status as ongoing */
            $this->setStatus(Idp::ONGOING);
        }

        if($save)
            $this->save();
    }    

    public function setAsDisapproved($save = false) {

        /* Check if for new request or update */
        if($this->isApproved()) {

            /* Disapprove temp */
            $this->removeTemp();

        } else {

            $this->approval_status = Idp::DISAPPROVED;
        }

        if($save)
            $this->save();
    }    

    public function setStatus($status) {
        $this->status = $status;
        $this->save();
    }

    public static function getRecentYears() {
        $array = [];

        /* Get current year */
        $current = Carbon::now();
        $next = Carbon::parse($current)->addYear();


        /* Add current & next to array */
        $array[] = $next->year;
        $array[] = $current->year;

        /* Fetch last 5 years */
        for($i = 1; $i <= 5; $i++) {
            $past = Carbon::parse($current)->subYears($i);

            $array[] = $past->year;
        }

        return $array;
    }

    public static function getLearningActivityType() {
        return [
            ['label' => 'None', 'value' => Idp::NONE],
            ['label' => 'Education', 'value' => Idp::EDUCATION],
            ['label' => 'Experience', 'value' => Idp::EXPERIENCE],
            ['label' => 'Exposure', 'value' => Idp::EXPOSURE],
        ];
    }

    public static function getCompetencyType() {
        return [
            ['label' => 'Technical', 'value' => Idp::TECHNICAL],
            ['label' => 'Values', 'value' => Idp::VALUES],
            ['label' => 'Leadership', 'value' => Idp::LEADERSHIP],
        ];
    }

    public static function getType() {
        return [
            ['label' => 'None', 'value' => Idp::NONE],
            ['label' => 'With gap', 'value' => Idp::WITHGAP],
            ['label' => 'Continuous learning', 'value' => Idp::CONTLEARNING],
            ['label' => 'Additional Competency', 'value' => Idp::ADDCOMPETENCY],
        ];
    } 

    public static function getStatus() {
        return [
            ['label' => 'Pending', 'class' => 'bg-yellow', 'value' => Idp::PENDING],
            ['label' => 'Ongoing', 'class' => 'bg-yellow', 'value' => Idp::ONGOING],
            ['label' => 'For Completion', 'class' => 'bg-orange', 'value' => Idp::FORCOMPLETION],
            ['label' => 'Completed', 'class' => 'bg-green', 'value' => Idp::COMPLETED],
        ];
    }

    public static function getApprovalStatus() {
        return [
            ['label' => 'Pending', 'class' => 'bg-yellow', 'value' => Idp::PENDING],
            ['label' => 'Approved', 'class' => 'bg-green', 'value' => Idp::APPROVED],
            ['label' => 'Disapproved', 'class' => 'bg-red', 'value' => Idp::DISAPPROVED],
            ['label' => 'Cancelled', 'class' => 'bg-orange', 'value' => Idp::CANCELLED],
        ];
    }      

    public static function getConstantValue($array, $label) {

        /* Loop through the array */
        foreach ($array as $obj) {
            
            if(strtolower($obj['label']) == strtolower($label))
                return $obj['value'];
        }

        return 0;
    }


    /*
    |-----------------------------------------------    
    | @Methods
    |-----------------------------------------------
    */
    public function saveTemp() {

        $tempIDP = $this->temp;
        $approver = \Auth::user();

        /* Fetch temp vars */
        $vars = $tempIDP->getVars();

        /* Update IDP */
        $this->update($vars);
        
        /* Approve temp IDP */
        $tempIDP->approve();
        

        /* Notify the user */
        $this->employee->notify(new IDPTempWasApproved($this, $approver));
    }

    public function removeTemp() {

        $tempIDP = $this->temp;
        $approver = \Auth::user();

        /* Disapprove temp IDP */
        $tempIDP->disapprove();


        /* Notify the user */
        $this->employee->notify(new IDPTempWasDisapproved($this, $approver));
    }    

    public function tempUpdate($request) {

        $user = \Auth::user();
        $vars = $request->except(['_token', 'employee_id']);

        /* Add necessary vars */
        $vars['idp_id'] = $this->id;
        $vars['employee_id'] = $this->employee->id;
        $vars['updater_id'] = $user->id;


        /* Check if there is a temp IDP */
        if($this->temp) {

            /* Update the temp IDP */
            $this->temp->update($vars);

            /* Add in approver */
            $this->addApprovers(0);


            /* Notify all approvers */
            // $this->notifyIDPUpdate($this->temp);

            return $this->temp;

        } else {

            /* Create the temp IDP */
            $temp = TempIdp::create($vars);

            /* Associate Temp IDP */
            $temp->idp()->associate($this->id);
            $temp->save();

            /* Add in approver */
            $this->addApprovers(0);


            /* Notify all approvers */
            // $this->notifyIDPUpdate($temp);

            return $temp;
        }
    }

    public function updateStatus($status) {
        $approver = \Auth::user();


        /* Check if you're one of the approver */
        if(!$this->isApprover($approver))
            return false;


        /* Get the approver object for the current user */
        $idpApprover = $this->approvers()->where('approver_id', $approver->id)->get()->first();
        $currApprover = $this->approvers()->where('status', IdpApprover::PENDING)->orderBy('sort')->get()->first();
        $nextApprover = !$currApprover ? null : $this->approvers()
                                                        ->where('status', 0)
                                                        ->where('id', '!=', $idpApprover->id)
                                                        ->orderBy('sort')
                                                        ->get()
                                                        ->first();


        /* Update the status */
        switch($status) {
            case IdpApprover::APPROVED:
                
                /* Update the approvers status to approve and disable it now */
                $idpApprover->approve();

                /* Enable the next approver */
                if($nextApprover) {

                    $nextApprover->setAsEnabled();

                    /* Notify the next approver */
                    $nextApprover->approver->notify(new IDPHasApprover($this, $idpApprover));
                }


                /* Check if all approval is done */
                if($this->checkIfAllApprove()) {

                    /* Update the form as approved */
                    $this->setAsApproved(1);
                }                

            break;
            case IdpApprover::DISAPPROVED:

                /* Change status to disapprove */
                $this->setAsDisapproved(1);

                /* Update the approvers status to disapprove */
                $idpApprover->disapprove('');

            break;
        }        


        return true;
    }

    public function addApprovers($isNew) {

        /* Check if idp approvers exist already */
        if($this->approvers()->count()) {

            /* Delete approvers */
            $this->approvers()->delete();
        }


        $isEnable = true;
        $sort = 0;

        /* Add in immediate leader as an approver */
        $immediateLeader = $this->employee->getImmediateLeader();

        if($immediateLeader) {
            $this->createApprover($immediateLeader->id, IdpApprover::IMMEDIATE_LEADER, $sort, $isEnable);

            /* Set enable as false */
            $isEnable = false;
            $sort++;
        }

        /* Add in approver depending on the IDP */
        switch($isNew) {
            case 1:

                /* Add in OD as an approver */
                $company = $this->employee->getCompany();

                if($company) {
                    if($company->od) {
                        $this->createApprover($company->od->id, IdpApprover::OD, $sort, $isEnable);

                        /* Set enable as false */
                        $isEnable = false;
                        $sort++;
                    }
                }

                /* Add in Group Head as an approver */
                $division = $this->employee->getDivision();

                if($division) {
                    if($division->group_head) {
                        $this->createApprover($division->group_head->id, IdpApprover::GROUP_HEAD, $sort, $isEnable);

                        /* Set enable as false */
                        $isEnable = false;
                        $sort++;
                    }
                }

            break;
            case 0;

                /* Add in Group Head as an approver */
                $division = $this->employee->getDivision();

                if($division) {
                    if($division->group_head) {
                        $this->createApprover($division->group_head->id, IdpApprover::GROUP_HEAD, $sort, $isEnable);

                        /* Set enable as false */
                        $isEnable = false;
                        $sort++;
                    }
                }

                /* Add in OD as an approver */
                $company = $this->employee->getCompany();

                if($company) {
                    if($company->od) {
                        $this->createApprover($company->od->id, IdpApprover::OD, $sort, $isEnable);

                        /* Set enable as false */
                        $isEnable = false;
                        $sort++;
                    }
                }

            break;
        }

        // http_response_code(500); dd($company->od->renderFullname());
    }

    public function createApprover($approverID, $type, $sort, $isEnable) {

        $vars = [];

        /* Set variable */
        $vars['idp_id'] = $this->id;
        $vars['approver_id'] = $approverID;
        $vars['type'] = $type;
        $vars['sort'] = $sort;
        $vars['enabled'] = $isEnable;


        /* Check if approver exist */
        $idpApprover = $this->approvers()->where('approver_id', $approverID)->get();

        if($idpApprover->count() > 0) {

            /* Set to pending again */
            $idpApprover = $idpApprover->first();
            $idpApprover->setAsPending();

        } else {

            /* Create the idp approver */
            $idpApprover = IdpApprover::create($vars);
        }


        /* Notify approver */
        if($isEnable)
            $idpApprover->approver->notify(new IDPHasApprover($this, $idpApprover));
    }    

    public function notifyIDPUpdate($idpTemp) {

        /* Fetch settings */
        $settings = Settings::get()->first();

        /* Notify OD */
        // $settings->od->notify(new IDPWasUpdated($this, $idpTemp, $this->employee, false));
    }


    /*
    |-----------------------------------------------
    | @Checker
    |-----------------------------------------------
    */
    public function isApproved() {
        return $this->approval_status == Idp::APPROVED;
    }

    public function isApprover($approver) {
        return $this->approvers()->where('approver_id', $approver->id)->exists();
    }

    public function checkIfAllApprove() {
        return !$this->approvers()->where('status', IdpApprover::PENDING)->exists();
    }    


    /*
    |-----------------------------------------------
    | @Render
    |-----------------------------------------------
    */
    public static function renderStatusFilter() {
        $array = [];

        /* Add in status options */
        $array[0] = [
            'label' => 'status',
            'options' => [],
        ];

        foreach (Idp::getStatus() as $key => $user) {
            array_push($array[0]['options'], [
                'id' => $user['value'],
                'label' => $user['label'],
            ]);
        }


        return $array;
    }   

    public function renderLearningActivityType() {
        return $this->renderConstantLabel(Idp::getLearningActivityType(), $this->learning_type);
    }   

    public function renderCompetencyType() {
        return $this->renderConstantLabel(Idp::getCompetencyType(), $this->competency_type);
    }

    public function renderType() {
        return $this->renderConstantLabel(Idp::getType(), $this->type);
    }
   
    public function renderStatus() {

        if($this->isApproved())
            return $this->renderConstantLabel(Idp::getStatus(), $this->status);

        return $this->renderConstantLabel(Idp::getApprovalStatus(), $this->approval_status);
    }

    public function renderApprovalStatus() {
        return $this->renderConstantLabel(Idp::getApprovalStatus(), $this->approval_status);
    }

    public function renderApprovalClass() {
        return $this->renderConstantClass(Idp::getApprovalStatus(), $this->approval_status);
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

    public function renderApprovalViewURL() {

        /* Check if there is a temp IDP */
        if($this->status > Idp::PENDING && $this->temp)
            return route('idptmp.show', $this->temp->id);

        return route('idp.show', $this->id);
    }

    public function renderViewURL() {
        return route('idp.show', $this->id);
    }
}
