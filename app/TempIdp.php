<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Notifications\Learnings\IDPWasUpdated;

use Laravel\Scout\Searchable;
use Carbon\Carbon;

use App\Idp;

class TempIdp extends Model
{
    use SoftDeletes;
    use Searchable;

    public $asYouType = true;

    /*
    |-----------------------------------------------
    | @Columns
    |-----------------------------------------------
    */
    const TABLE_COLUMNS = [
        'id', 'employee_id', 'competency_id', 'approver_id',
        'learning_type', 'competency_type', 'required_proficiency_level', 'current_proficiency_level', 'type',
        'details', 'completion_year',
        'state', 'status', 'approval_status',
    ]; 

    /*
    |-----------------------------------------------
    | @State
    |-----------------------------------------------
    */
    const PENDING = 0;
    const APPROVED = 1;
    const DISAPPROVED = 2;


    protected $guarded = [];
    protected $appends = ['extra'];    


    public function idp() {
        return $this->belongsTo(Idp::class);
    }

    public function employee() {
        return $this->belongsTo(User::class, 'employee_id')->withTrashed();
    }

    public function competency() {
        return $this->belongsTo(IdpCompetency::class, 'competency_id');
    }    

    public function updater() {
        return $this->belongsTo(User::class, 'updater_id')->withTrashed();
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
            'employee' => $this->employee->renderFullname(),
            'learning_activity_type' => $this->renderLearningActivityType(),
            'competency_type' => $this->renderCompetencyType(),
            'required_proficiency_level' => $this->required_proficiency_level,
            'current_proficiency_level' => $this->current_proficiency_level,
            'status' => $this->renderStatus(),
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
            'view' => $this->renderViewURL(),
        ];
    }

    public function getVars() {

        /* Fetch & create unneeded vars array */
        $remove = ['id', 'idp_id', 'employee_id', 'approver_id', 'employee', 'approver', 'extra', 'state', 'status', 'approval_status', 'deleted_at', 'created_at', 'updated_at'];

        /* Create a tmp var for to convert into an array */
        $vars = $this->getAttributes();


        /* Remove unneeded vars */
        $vars = array_diff_key($vars, array_flip($remove));

        return $vars;
    }

    public static function getState() {
        return [
            ['label' => 'Pending', 'class' => 'label-warning', 'value' => TempIdp::PENDING],
            ['label' => 'Approved', 'class' => 'label-success', 'value' => TempIdp::APPROVED],
            ['label' => 'Disapproved', 'class' => 'label-danger', 'value' => TempIdp::DISAPPROVED],
        ];
    }    


    /*
    |-----------------------------------------------    
    | @Methods
    |-----------------------------------------------
    */
   	public function approve() {

        /* Set status to approved */
        $this->approver_id = \Auth::user()->id;
        $this->state = TempIdp::APPROVED;
        $this->save();

        /* Archive the idp */
        $this->delete();
    }

    public function disapprove() {

        /* Set status to disapproved */
        $this->approver_id = \Auth::user()->id;
        $this->state = TempIdp::DISAPPROVED;
        $this->save();

        /* Archive the idp */
        $this->delete();
    }

    public function transferApproval() {

        /* Fetch supervisor/immediate leader */
        $approver = $this->idp->employee->supervisor;


        /* Set approver to immediate leader */
        $this->approver_id = $approver->id;
        $this->save();


        /* Notify new approver */
        $approver->notify(new IDPWasUpdated($this->idp, $this, $this->employee, false));        
    }

    public function addApprovers() {
        
    }


    /*
    |-----------------------------------------------
    | @Checker
    |-----------------------------------------------
    */
    public function isApproved() {
        return $this->state == TempIdp::APPROVED;
    }


    /*
    |-----------------------------------------------
    | @Render
    |-----------------------------------------------
    */   
    public function renderLearningActivityType() {
        return $this->renderConstantLabel(Idp::getLearningActivityType(), $this->learning_activity_type);
    }

    public function renderCompetencyType() {
        return $this->renderConstantLabel(Idp::getCompetencyType(), $this->competency_type);
    }

    public function renderType() {
        return $this->renderConstantLabel(Idp::getType(), $this->type);
    }    

    public function renderStatus() {
        return $this->renderConstantLabel(Idp::getStatus(), $this->status);
    } 

    public function renderState() {
        return $this->renderConstantLabel(TempIdp::getState(), $this->state);
    }

    public function renderStateClass() {
        return $this->renderConstantClass(TempIdp::getState(), $this->state);
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
        return route('idptmp.show', $this->id);
    }     
}
