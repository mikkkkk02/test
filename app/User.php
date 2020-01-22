<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

use App\Notifications\User\UserWasAssigned;
use App\Notifications\Requests\RequestHasApprover;
use App\Notifications\Events\EventWillAttend;

use Laravel\Scout\Searchable;
use Carbon\Carbon;

use App\DepartmentEmployee;
use App\Form;
use App\FormApprover;
use App\FormTemplateApprover;
use App\IdpApprover;
use App\EventParticipant;
use App\Idp;
use App\Ticket;

class User extends Authenticatable
{
    use Notifiable;
    use Searchable;
    use SoftDeletes;

    public $asYouType = true;

    /*
    |-----------------------------------------------
    | @Columns
    |-----------------------------------------------
    */
    const MINIMAL_COLUMNS = [
        'id', 'department_employee_id', 'employee_category_id', 'supervisor_id', 'location_id',
        'email', 'first_name', 'middle_name', 'last_name',
    ];
    const TABLE_COLUMNS = [
        'id', 'department_employee_id', 'employee_category_id', 'supervisor_id', 'location_id',
        'email', 'first_name', 'middle_name', 'last_name',
    ];

    /*
    |-----------------------------------------------
    | @Withs
    |-----------------------------------------------
    */    
    const TABLE_WITHS = [
        'department', 'department.team', 'department.position', 'department.department',
        'department.department.division', 'department.department.division.company',
    ];

    /*
    |-----------------------------------------------
    | @Type
    |-----------------------------------------------
    */
    const NOTVERIFIED = 0;
    const ENABLE = 1;
    const DISABLE = 2;

    /*
    |-----------------------------------------------
    | @Job Level
    |-----------------------------------------------
    */
    const STAFF = 0;
    const SUPERVISOR = 1;
    const MANAGER = 2;
    const EXECUTIVE = 3;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'department_employee_id', 'employee_category_id', 'supervisor_id', 'location_id',

        'first_name', 'middle_name', 'last_name', 
        'contact_no', 'company_no',
        'address_line1', 'address_line2',
        'job_level', 'job_grade', 'cost_center',

        'email',  'password',
        'google_id', 'google_name',

        'profile_photo',

        'onVacation',
        'vacation_proxy_id',
        'vacation_start_date',
        'vacation_end_date',

        'verify_token',
    ];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        'status', 'notification',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'google_id', 'password', 'remember_token', 
    ];

    protected $appends = ['extra'];


    public function groups() {
        return $this->belongsToMany(Group::class)->withTrashed();
    }  
    
    public function photo() {
        return $this->belongsTo(ProfilePhoto::class);
    }

    public function location() {
        return $this->belongsTo(Location::class)->withTrashed();
    }

    public function department() {
        return $this->belongsTo(DepartmentEmployee::class, 'department_employee_id');
    }

    public function category() {
        return $this->belongsTo(EmployeeCategory::class, 'employee_category_id');
    }

    public function supervisor() {
        return $this->belongsTo(User::class, 'supervisor_id')->withTrashed();
    }

    public function forms() {
        return $this->hasMany(Form::class, 'employee_id');
    }

    public function temp_forms() {
        return $this->hasMany(TempForm::class, 'employee_id');
    }    

    public function assignee_forms() {
        return $this->hasMany(Form::class, 'assignee_id');
    }    

    public function form_updates() {
        return $this->hasMany(FormUpdate::class, 'employee_id');
    }    

    public function idps() {
        return $this->hasMany(Idp::class, 'employee_id');
    }    

    public function temp_idps() {
        return $this->hasMany(TempIdp::class, 'employee_id');
    }

    public function events() {
        return $this->hasMany(EventParticipant::class, 'participant_id');
    }    

    public function learnings() {
        return $this->hasMany(LearningParticipant::class, 'participant_id');
    }        

    public function approvals() {
        return $this->hasMany(FormApprover::class, 'approver_id');
    }

    public function update_approvals() {
        return $this->hasMany(TempFormApprover::class, 'approver_id');
    }    

    public function idp_approvals() {
        return $this->hasMany(IdpApprover::class, 'approver_id');
    } 

    public function ticket_approvals() {
        return $this->hasMany(Ticket::class, 'technician_id');
    }

    public function event_approvals() {
        return $this->hasMany(EventParticipant::class, 'approver_id');
    }

    public function learning_approvals() {
        return $this->hasMany(LearningParticipant::class, 'approver_id');
    }

    public function tickets() {
        return $this->hasMany(Ticket::class, 'employee_id');
    }

    public function ticket_updates() {
        return $this->hasMany(TicketUpdate::class, 'employee_id');
    }    

    public function form_template_approvals() {
        return $this->hasMany(FormTemplateApprover::class, 'approver_id');
    }

    public function subordinates() {
        return $this->hasMany(User::class, 'supervisor_id');
    }

    public function proxy() {
        return $this->belongsTo(User::class, 'vacation_proxy_id')->withTrashed();
    }    

    public function assignees() {
        return $this->belongsToMany(User::class, 'assignee_user', 'assigner_id', 'user_id');
    }

    public function created_mr_reservations() {
        $this->hasMany(MrReservation::class, 'creator_id');
    }

    public function updated_mr_reservations() {
        $this->hasMany(MrReservation::class, 'updater_id');
    }

    public function created_mr_times() {
        $this->hasMany(MrReservationTime::class, 'creator_id');
    }

    public function updated_mr_times() {
        $this->hasMany(MrReservationTime::class, 'updater_id');
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
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'name' => $this->renderFullname(),
            'contact_no' => $this->contact_no,
            'company_no' => $this->contact_no,
            'email' => $this->email,
            'location' => $this->location ? $this->location->name : null,

            'leader' => $this->supervisor ? $this->supervisor->renderFullname() : '',
            'category' => $this->category ? $this->category->title : '',            
        ];

        /* Check if the user has department */
        if($this->department) {

            $holder = $this->department;

            $searchable['department'] = $holder->department->name;
            $searchable['division'] = $holder->department->division->name;
            $searchable['company'] = $holder->department->division->company->name;

            /* Include company abbreviation if it has */
            if($holder->department->division->company->abbreviation)
                $searchable['company_abbr'] = $holder->department->division->company->abbreviation;

            /* Check if team exists */
            if($holder->department->team)
                $searchable['team'] = $holder->department->team->name;
        }

        return $searchable;
    }


    /*
    |-----------------------------------------------
    | @Helper
    |-----------------------------------------------
    */
    public function getExtraAttribute() {
        return $this->attributes['extra'] = [
            'photo' => $this->getProfilePhoto(),
            'fullname' => $this->renderFullname(),
            'view' => $this->renderViewURL(),
        ];
    }

    public function getProfilePhoto() {
        return $this->profile_photo ? '/storage/' . $this->profile_photo : asset('image/default-profile.jpg') ;
    }

    public function setAsEnable() {
        return $this->status = User::ENABLE;
    }

    public function setAsDisable() {
        return $this->status = User::DISABLE;
    }

    public function getCompany() {

        /* Check if company is set */
        if(!$this->department || !$this->department->department || !$this->department->department->division || !$this->department->department->division->company)
            return false;

        return $this->department->department->division->company;
    }

    public function getDivision() {

        /* Check if division is set */    
        if(!$this->department || !$this->department->department || !$this->department->department->division)
            return false;

        return $this->department->department->division;
    } 

    public function getDepartment() {

        /* Check if deoartment is set */    
        if(!$this->department || !$this->department->department)
            return false;

        return $this->department->department;
    }    

    public function getTeam() {

        /* Check if team is set */
        if(!$this->department || !$this->department->team)
            return false;

        return $this->department->team;
    }

    public function setSupervisor($supervisor) {
        $this->supervisor_id = $supervisor->id;
        $this->save();
    }

    public function removeSupervisor() {
        $this->supervisor_id = null;
        $this->save();
    }

    public function getImmediateLeader() {
        $supervisor = $this->supervisor ? $this->supervisor : false;


        /* Check if supervisor exists */
        if(!$supervisor)
            return false;

        /* Check if supervisor is on vacation */
        if($supervisor->isOnVacation())
            $supervisor = $this->proxy;

        return $supervisor;
    }

    public function getImmediateLeaderID() {
        $supervisor = $this->getImmediateLeader();


        /* Check if the user has an immediate leader */
        if($supervisor)
            return $supervisor->id;

        return false;
    }

    public function getNextLevelLeader() {

        /* Check if the user has an immediate leader */
        if(!$this->getImmediateLeader())
            return false;


        /* Fetch next level leader */
        $leader = $this->supervisor->supervisor;

        /* Check if the user has an next level leader */
        if(!$leader)
            return false;

        /* Check if supervisor is on vacation */
        if($leader->isOnVacation())
            $leader = $leader->proxy;

        return $leader;
    }

    public function getNextLevelLeaderID() {
        $supervisor = $this->getNextLevelLeader();

        /* Check if the user has a next level leader */
        if($supervisor)
            return $supervisor->id;

        return false;
    }

    public function getGroupsID() {
        return $this->groups->pluck('id')->toArray();
    }

    public function getAssigneesID() {
        return $this->assignees->pluck('id')->toArray();
    }

    public function getSubordinateID($includedUsers = []) {
        $array = $this->subordinates()->pluck('id')->toArray();

        foreach($this->subordinates as $key => $user) {

            if(!count(array_intersect($includedUsers, [$user->id]))) {

                /* Add in user ID to the checked array */
                array_push($includedUsers, $user->id);

                array_merge($array, $user->getSubordinateID($includedUsers));
            }
        }
 
        return $array;
    }
       
    public function getHandledCompanies($role) {
        $companies = [];


        foreach($this->groups as $key => $group) {
            
            if($group->hasRole($role)) {

                /* Check if company is null, meaning all */
                if($group->company == null)
                    return Company::select(Company::MINIMAL_COLUMNS)->get();

                /* Add in company to array */
                array_push($companies, $group->company);
            }
        }

        return $companies;
    }

    public function getSubordinateCount() {
        return $this->subordinates()
                    ->count();
    }

    public function getRequestApprovalCount() {
        return $this->approvals()
                    ->join('forms', 'form_id', '=', 'forms.id')
                    ->select(['form_approvers.status AS form_approvers.status', 'form_approvers.enabled AS form_approvers.enabled', 'form_id', 'forms.status AS form.status'])
                    ->where('form_approvers.status', FormApprover::PENDING)
                    ->where('forms.status', '!=', Form::CANCELLED)
                    ->where('forms.status', '!=', Form::DISAPPROVED)
                    ->where('forms.status', '!=', Form::APPROVED)
                    ->where('form_approvers.enabled', 1)
                    ->get()
                    ->count();
    }

    public function getRequestUpdateApproval() {
        return $this->update_approvals()
                    ->join('temp_forms', 'temp_form_id', '=', 'temp_forms.id')
                    ->select(['temp_form_approvers.status AS temp_form_approvers.status', 'temp_form_approvers.enabled AS temp_form_approvers.enabled', 'temp_form_id', 'temp_forms.status AS temp_forms.status'])
                    ->where('temp_form_approvers.status', FormApprover::PENDING)
                    ->where('temp_form_approvers.enabled', 1)
                    ->get();
    }

    public function getRequestUpdateApprovalCount() {
        return $this->getRequestUpdateApproval()
                    ->count();
    }    

    public function getEventApprovalCount() {
        return $this->event_approvals()
                    ->select('status')
                    ->where('status', EventParticipant::PENDING)
                    ->get()
                    ->count();
    }

    public function getIDPApprovalCount() {
        return $this->idp_approvals()
                    ->join('idps', 'idp_id', '=', 'idps.id')
                    ->select(['idp_approvers.status AS idp_approvers.status', 'idp_approvers.enabled AS idp_approvers.enabled', 'idp_id', 'idps.approval_status AS idp.approval_status'])
                    ->where('idp_approvers.enabled', 1)
                    ->where('idp_approvers.status', IdpApprover::PENDING)
                    ->get()
                    ->count();
    } 

    public function getTicketApproval() {
        return $this->ticket_approvals()
                    ->select('id', 'employee_id', 'technician_id', 'status')
                    ->where('technician_id', $this->id)
                    ->whereIn('status', [Ticket::OPEN, Ticket::ONHOLD])
                    ->get();
    }

    public function getTicketApprovalCount() {
        return $this->getTicketApproval()->count();
    }

    public static function getJobLevel() {
        return [
            ['label' => 'Staff', 'color' => 'bg-yellow', 'value' => User::STAFF],
            ['label' => 'Supervisor', 'color' => 'bg-green', 'value' => User::SUPERVISOR],
            ['label' => 'Manager', 'color' => 'bg-red', 'value' => User::MANAGER],
            ['label' => 'Executive', 'color' => 'bg-red', 'value' => User::EXECUTIVE],
        ];
    } 


    /*
    |-----------------------------------------------    
    | @Methods
    |-----------------------------------------------
    */
    public function resetPassword() {
        $newPassword = str_random(10);

        $this->password = \Hash::make($newPassword);
        $this->save();

        return $newPassword;
    }

    public function addGroup($group) {
        $this->groups()->save($group);
    }

    public function removeGroup($group) {
        $this->groups()->detach($group);
    }

    public function updateGroups($groups) {
        $this->groups()->sync($groups);
    }

    public function assignDepartment($departmentID, $positionID = null, $teamID = null) {

        /* Delete previous department */
        $this->unassignDepartment();

        /* Create & Assign the department to the user */
        $this->department()->associate(
            DepartmentEmployee::create([
                'employee_id' => $this->id,
                'department_id' => $departmentID,

                'position_id' => $positionID ? $positionID : null,
                'team_id' => $teamID ? $teamID : null,
            ])
        );

        $this->save();
    }

    public function unassignDepartment() {

        /* Delete previous department */
        if($this->department)
            $this->department()->delete();
    }

    public function updateAssignee($employees) {

        /* Sync the assignees */
        $this->assignees()->sync($employees);


        /* Notify all assignees */
        foreach ($this->assignees as $key => $assignee) {
            $assignee->notify(new UserWasAssigned($this, $assignee));
        }
    }

    public function updateSupervisor($supervisor) {

        /* Fetch needed vars */
        $newSupervisor = User::find($supervisor);

        if (!$newSupervisor) {
            return false;
        }

        $oldSupervisor = $this->supervisor;


        /* Update supervisor */
        $this->supervisor_id = $newSupervisor->id;
        $this->save();

        /* Check if supervisor exists */
        if($newSupervisor && $oldSupervisor) {

            /*
            | @Update request approvers
            |-----------------------------------------------*/
            $formApprovers = FormApprover::whereIn('form_id', $this->forms()->where('status', '!=', Form::APPROVED)
                                                                        ->pluck('id')
                                                                        ->toArray()
                                            )
                                        ->where('status', FormApprover::PENDING)
                                        ->get();


            /* Update each request and send in the notification */
            foreach($formApprovers as $key => $formApprover) {

                /* Check if approver is for the immediate leader */
                if($formApprover->form_template_approver &&
                    $formApprover->form_template_approver->type == FormTemplateApprover::LEVEL) {

                    /* Check depending on the level */
                    switch($formApprover->form_template_approver->type_value) {
                        case FormTemplateApprover::IMMEDIATE_LEADER:
                     
                            /* Update the approver */
                            $this->updateApprover($formApprover, $newSupervisor);

                        break;
                        case FormTemplateApprover::NEXT_LEVEL_LEADER:

                            /* Update the approver */
                            $this->updateApprover($formApprover, $newSupervisor->supervisor);

                        break;                        
                    }
                }
            }

            /* Update also all next level leader approvers */
            $this->updateNextLevelLeader($newSupervisor, $oldSupervisor);


            /*
            | @Update event approvers
            |-----------------------------------------------*/
            $events = $this->events()->where('status', EventParticipant::PENDING)
                                        ->get();            

            /* Update approver on events */
            foreach($events as $key => $event) {

                /* Update supervisor if needed */
                if($event->approver_id != $newSupervisor->id) {

                    $event->approver_id = $newSupervisor->id;
                    $event->save();


                    /* Notify new approver */
                    $event->approver->notify(new EventWillAttend($event, $event->employee, false));
                }                
            }             
        }
    }

    public function updateNextLevelLeader($newSupervisor, $oldSupervisor) {

        /* Check if leader exists */
        if($oldSupervisor && $newSupervisor) {

            /* Fetch all subordinate forms */
            $formIDs = Form::whereIn('employee_id', $this->subordinates()->pluck('id')->toArray())
                            ->where('status', '!=', [FormApprover::APPROVED, FormApprover::DISAPPROVED])
                            ->pluck('id')
                            ->toArray();

            /* Fetch all form approvers for the old supervisor */
            $formApprovers = FormApprover::whereIn('id', $formIDs)
                                            ->where('status', FormApprover::PENDING)
                                            ->where('approver_id', $oldSupervisor->id)
                                            ->get();

            /* Update each request and send in the notification */
            foreach($formApprovers as $key => $formApprover) {

                /* Check if approver is for the immediate leader */
                if($formApprover->form_template_approver &&
                    $formApprover->form_template_approver->type == FormTemplateApprover::LEVEL &&
                    $formApprover->form_template_approver->type_value == FormTemplateApprover::NEXT_LEVEL_LEADER) {

                    /* Update the approver */
                    $this->updateApprover($formApprover, $newSupervisor);
                }
            }
        }
    }

    private function updateApprover($formApprover, $newApprover) {

        /* Update approver */
        $formApprover->approver_id = $newApprover->id;
        $formApprover->save();

        /* Check if notification is needed */
        if($formApprover->enabled)
            $formApprover->approver->notify(new RequestHasApprover($formApprover->form, $formApprover));        
    }

    public function setAsVerified() {

        if(!$this->status) {
            $this->setAsEnable();
        }
    }


    /*
    |-----------------------------------------------
    | @Checker
    |-----------------------------------------------
    */
    public function isApprover($id) {
        return in_array($id, $this->approvals()->pluck('form_id')->toArray());
    }

    public function isUpdateApprover($id) {
        return in_array($id, $this->update_approvals()->pluck('temp_form_id')->toArray());
    }    

    public function isIdpApprover($id) {
        return in_array($id, $this->idp_approvals()->pluck('idp_id')->toArray());
    }    

    public function isOnVacation() {
        $startDate = Carbon::parse($this->vacation_start_date);
        $endDate = Carbon::parse($this->vacation_end_date);


        /* Check if on vacation & between the start and end date set */
        if($this->onVacation && Carbon::now()->between($startDate, $endDate))
            return true;

        return false;
    }   

    public function isExecutiveUp() {
        return $this->job_level >= User::EXECUTIVE;
    }

    public function isManager() {
        return $this->job_level == User::MANAGER;
    }

    public function isVerified() {
        return $this->status;
    }
        
    public function isSuperUser() {
        return $this->groups->contains('id', 1);
    }

    public function hasRole($role) {

        /* Check each User's group if role is available */
        foreach ($this->groups as $group) {

            if($group->hasRole($role))
                return true;
        }

        return false;
    }    

    public static function isJobLevel($string) {
        foreach (User::getJobLevel() as $obj) {
            
            if(strtolower($obj['label']) == strtolower($string))
                return $obj['value'];
        }

        return null;
    }


    /*
    |-----------------------------------------------
    | @Render
    |-----------------------------------------------
    */
    public static function renderTableFilter() {
        $array = [];

        /* Add in employee category options */
        array_push($array, EmployeeCategory::renderFilterArray());


        return $array;
    }

    public static function renderEmployeeFilter() {
        $array = [];

        /* Add in status options */
        $array[0] = [
            'label' => 'employee',
            'options' => [],
        ];

        foreach (User::select(User::MINIMAL_COLUMNS)->orderBy('last_name', 'asc')->get() as $key => $user) {
            array_push($array[0]['options'], [
                'id' => $user->id,
                'label' => $user->renderFullname(),
            ]);
        }


        return $array;
    }

    public function renderSubordinateFilter() {
        $array = [];

        /* Add in status options */
        $array[0] = [
            'label' => 'employee',
            'options' => [],
        ];

        foreach ($this->subordinates as $key => $subordinate) {
            array_push($array[0]['options'], [
                'id' => $subordinate->id,
                'label' => $subordinate->renderFullname(),
            ]);
        }


        return $array;
    }

    public function renderJobLevel() {
        return $this->renderConstantLabel(User::getJobLevel(), $this->job_level);
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

    public function renderFullname() {
        return $this->last_name . ', ' . $this->first_name . ($this->middle_name ? ' ' . $this->middle_name : '') . ($this->suffix ? ', ' . $this->suffix : '');
    }

    public function renderFullname2() {
        return $this->first_name . ' ' . ($this->middle_name ?  $this->middle_name . ' ' : '') . $this->last_name . ($this->suffix ? ' ' . $this->suffix : '');
    }

    public function renderContact() {
        return $this->company_no . ($this->contact_no ? ' / ' . $this->contact_no : ''); 
    }    

    public function renderViewURL() {
        return route('employee.show', $this->id); 
    }
}
