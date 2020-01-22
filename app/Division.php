<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Laravel\Scout\Searchable;

use App\Notifications\Requests\RequestHasApprover;

use App\FormApprover;
use App\FormTemplateApprover;

class Division extends Model
{
    use SoftDeletes;    
    use Searchable;

    public $asYouType = true;

    /*
    |-----------------------------------------------
    | @Columns
    |-----------------------------------------------
    */
    const MINIMAL_COLUMNS = [
        'id', 'company_id',
        'name',
    ];
    const TABLE_COLUMNS = [
        'id', 'company_id',
        'name', 'description',
    ];    

    protected $guarded = [];
    protected $appends = ['extra'];


    public function company() {
    	return $this->belongsTo(Company::class)->withTrashed();
    }

    public function group_head() {
        return $this->belongsTo(User::class, 'group_head_id')->withTrashed();
    }

    public function departments() {
    	return $this->hasMany(Department::class);
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
            'name' => $this->name,
            'description' => $this->description,
            'company' => $this->company ? $this->company->name : null,
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
            'view' => $this->renderViewURL(),
            'departments' => $this->renderDepartmentCount(),
            'fetchdepartments' => $this->renderFetchDepartmentsURL(),
        ];
    }


    /*
    |-----------------------------------------------    
    | @Methods
    |-----------------------------------------------
    */
    public function assignGroupHead($employeeID) {

        /* Fetch needed vars */
        $newGroupHead = User::find($employeeID)->first();
        $tmpGroupHeadId = $this->group_head_id;

        /* Check if employee exists */
        if(!$newGroupHead || !$this->group_head_id)
            return false;


        /* Check group head */
        if($newGroupHead->id != $tmpGroupHeadId) {
            /* Update all pending group head approver */
            $this->updateGroupHeadApprovers($tmpGroupHeadId, $newGroupHead);
        }


        /* Update the group head */
        $this->group_head()->associate($newGroupHead);
        $this->save();
    }

    private function updateGroupHeadApprovers($oldApprover, $newApprover) {

        /* Fetch all included form approvers */
        $formApprovers = FormApprover::where('status', FormApprover::PENDING)
                                        ->where('type', FormTemplateApprover::GROUP_HEAD)
                                        ->where('approver_id', $oldApprover)
                                        ->get();

                                        
        /* Update each request and send in the notification */
        foreach($formApprovers as $key => $formApprover) {

            /* Check if owner is part of this division */
            $division = $formApprover->form->employee->getDivision();
            
            if($division && $division->id == $this->id) {

                /* Update the approver */
                $this->updateApprover($formApprover, $newApprover);
            }
        }
    }

    private function updateApprover($formApprover, $newApprover) {

        /* Check if new approver exists */
        if($newApprover) {
            
            /* Update approver */
            $formApprover->approver_id = $newApprover->id;
            $formApprover->save();

            /* Check if notification is needed */
            if($formApprover->enabled)
                $formApprover->approver->notify(new RequestHasApprover($formApprover->form, $formApprover));        
        }
    }

    public function assignCompany($company) {
        $this->company()->associate($company);
        $this->save();
    }

    public function unassignCompany() {
        $this->company()->dissociate();
        $this->save();        
    }  

    public function addDepartments($departments) {
        foreach ($departments as $department) {
            $department = Department::find($department);

            $department->assignDivision($this);
        }
    }

    public function removeDepartments($departments) {
        foreach ($departments as $department) {
            $department = Department::find($department);

            $department->unassignDivision();
        }
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
    public function renderDepartmentCount() {
        return $this->departments()->count();
    }   

    public function renderFetchDepartmentsURL() {
        return route('division.fetchdepartments', $this->id); 
    }   

    public function renderViewURL() {
        return route('division.show', $this->id); 
    }  
}
