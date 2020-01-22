<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests\ImportPost;

use App\Notifications\Requests\RequestHasApprover;
use App\Notifications\Events\EventWillAttend;

use Excel;

use App\User;
use App\Group;
use App\EmployeeCategory;
use App\Company;
use App\Division;
use App\Department;
use App\DepartmentEmployee;
use App\Team;
use App\Position;
use App\Location;

use App\Form;
use App\FormApprover;
use App\EventParticipant;

class UserImportController extends Controller
{
	protected $user;
    protected $companies;

    protected $logs = ['success' => [], 'errors' => []];

    /**
     * Instantiate a new UserImportController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('App\Http\Middleware\Users\ImportUserMiddleware', ['only' => ['import']]);
    }

    /**
     * Import a resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function import(ImportPost $request)
    {
        /* Check if there is a file uploaded */
        if($request->hasFile('file')) {

            $overwrite = $request->input('overwrite');

            $file = $request->file('file');
            $path = $file->getRealPath();
            $rowArray = [];


            /* Process excel data array */
            Excel::load($path, function($reader) use (&$rowArray) {

                /* Fetch data array */
                $rowArray = $reader->all()->toArray();
            });


            /* Check if file has all needed columns */
            return $this->checkImportColumn($rowArray, $overwrite);
        }
    }

    /**
     * Check uploaded file's columns
     *
     * @param  Array  $data
     * @return \Illuminate\Http\Response
     */
    public function checkImportColumn($data, $overwrite)
    {
        $important = ['employee_number', 'email', 'first_name', 'middle_name', 'last_name', 'suffix', 'company', 'group', 'department', 'team', 'position', 'location', 'assignment_category', 'supervisor_employee_number', 'job_level', 'cost_center', 'group_head'];

        foreach($important as $key => $value) {
            
            if(!array_key_exists($value, $data[0])) {
                return response()->json([
                    'response' => 0,
                    'message' => "Column " . str_replace('_', ' ', $value) . " is missing from the imported file",
                    'logs' => ['success' => '', 'errors' => ''],
                ]);
            }
        }

        /* Update or create IDP data */
        return $this->updateOrCreate($data, $overwrite);
    }

    /**
     * Import a resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateOrCreate($data, $overwrite)
    {
        /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();


        /* Fetch user */
        $this->user = \Auth::user();

        /* Fetch handled companies */
        $companies = $this->user->getHandledCompanies('Adding/Editing of Employee Profile');
        $this->companies = array_map(function($ar) {
                                return $ar['id'];
                            }, is_array($companies) ? $companies : $companies->toArray());

        
        $method = '';


        /* Loop through each sheet */
        foreach($data as $key => $row) {

            /* Check if employee no data exists */
            if(isset($row['employee_number']) && isset($row['email'])) {

                $empNo = $row['employee_number'];
                $email = $row['email'];

                $employee = User::withTrashed()->where(function ($query) use ($empNo, $email) {
                                $query->where('id', $empNo)
                                    ->orWhere('email', $email);
                            })
                            ->get()
                            ->first();

                $vars = [
                	'id' => $empNo,
					'email' => $row['email'],
					'first_name' => $row['first_name'],
					'middle_name' => $row['middle_name'], 
					'last_name' => $row['last_name'], 
					'suffix' => $row['suffix'], 
					'employee_category_id' => EmployeeCategory::updateOrCreate(['title' => $row['assignment_category']])->id,
					'location_id' => Location::updateOrCreate(['name' => $row['location']])->id,
					'supervisor_id' => $row['supervisor_employee_number'] ? $row['supervisor_employee_number'] : 0,
                    'job_level' => User::isJobLevel($row['job_level']),
                    'cost_center' => $row['cost_center']
                ];

                /* Fetch the company */
                $company = Company::where('name', $row['company'])->get()->first();
                $comCollection = $company ? Collect($company->id) : false;

                /* Check permission to edit company */
                if($this->user->isSuperUser() || $comCollection && $comCollection->intersect($this->companies)->count()) {
                    /* Check if user exist */
                    if($employee) {
                        /* Check if overwrite is enabled */
                        if($overwrite) {

                            $employeeEmailCheck = User::withTrashed()->where('id', '!=', $empNo)
                                                    ->where('email', $email)
                                                    ->get()
                                                    ->first();


                            /* Check duplicate email */
                            if(($employee->email == $email) || !$employeeEmailCheck) {

                                $employeeNoCheck = User::withTrashed()->where('id', $empNo)
                                                        ->where('email', '!=', $email)
                                                        ->get()
                                                        ->first();


                                /* Check duplicate emp. no */
                                if(($employee->id == $empNo) || !$employeeNoCheck) {

                                    /* Add additional vars */
                                    $vars['updater_id'] = $this->user->id;

                                    /* Update pending approvals */
                                    $this->updateRequests($employee, $row);

                                    /* Update organization */
                                    $this->updateOrganization($employee, $row, $key);

                                    /* Update employee */
                                    $this->updateObjects($employee, $row, $vars);


                                    $this->logs['success'][] = "* Line " . ($key + 2) . ": Successfully updated <a href='" . $employee->renderViewURL() . "'>" . $employee->renderFullname() . "</a> \n";


                                } else {
                                    $this->logs['errors'][] = "* Line " . ($key + 2) . ": Employee no. " . $empNo . " is already taken, please check if the employee no. is correct." . "\n";
                                }
                            } else {
                                $this->logs['errors'][] = "* Line " . ($key + 2) . ": Email address " . $email . " is already taken, please check if the email address is correct." . "\n";
                            }
                        } else {
                            $this->logs['errors'][] = "* Line " . ($key + 2) . ": Ignoring existing Employee no. " . $empNo . " for overwrite option is disabled" . "\n";
                        }

                    } else {

                        /* Add additional vars */
                        $vars['creator_id'] = $this->user->id;
                        $vars['updater_id'] = $this->user->id;


                        /* Create employee */
                        $employee = User::create($vars);
                        /* Set `Employee Self Service` group */
                        $employee->addGroup(Group::find(2));

                        /* Update organization */
                        $this->updateOrganization($employee, $row, $key);


                        $this->logs['success'][] = "* Line " . ($key + 2) . ": Successfully created <a href='" . $employee->renderViewURL() . "'>" . $employee->renderFullname() . "</a> \n";
                    }

                } else {

                    $this->logs['errors'][] = "* Line " . ($key + 2) . ": Unauthorized access to add/edit to the Company " . $row['company'] . "\n";
                }                    
            } else {


                $this->logs['errors'][] = "* Line " . ($key + 2) . ": Missing employee no. or email \n";                
            }
        }


        /*
        | @End Transaction
        |---------------------------------------------*/
        \DB::commit();


        return response()->json([
            'response' => 1,
            'message' => 'Successfully updated employee records base on the uploaded file',
            'logs' => $this->logs,
        ]);
    }

    /**
     * Update the users objects
     *
     */
    public function updateObjects($employee, $data, $vars) {

        $relatedGroupIDs = [];
        $relatedAssigneeIDs = [];
        $hasIDChanged = false;


        /* Check if id changes */
        if($employee->id != $data['employee_number']) {

            $hasIDChanged = true;


            /*
            | @Update groups
            |-----------------------------------------------*/
            $relatedGroupIDs = $employee->groups()->pluck('id');
            $employee->groups()->detach();

            /*
            | @Update assignees
            |-----------------------------------------------*/
            $relatedAssigneeIDs = $employee->assignees()->pluck('id');
            $employee->assignees()->detach();            

            /*
            | @Update forms
            |-----------------------------------------------*/
            $employee->forms()->update(['employee_id' => $data['employee_number']]);

            /*
            | @Update form updates
            |-----------------------------------------------*/
            $employee->form_updates()->update(['employee_id' => $data['employee_number']]);            

            /*
            | @Update idps
            |-----------------------------------------------*/
            $employee->idps()->update(['employee_id' => $data['employee_number']]);
            $employee->temp_idps()->update(['employee_id' => $data['employee_number']]);

            /*
            | @Update events
            |-----------------------------------------------*/
            $employee->events()->update(['participant_id' => $data['employee_number']]);

            /*
            | @Update learnings
            |-----------------------------------------------*/
            $employee->learnings()->update(['participant_id' => $data['employee_number']]); 

            /*
            | @Update forms approvals
            |-----------------------------------------------*/
            $employee->approvals()->update(['approver_id' => $data['employee_number']]);

            /*
            | @Update idp approvals
            |-----------------------------------------------*/
            $employee->idp_approvals()->update(['approver_id' => $data['employee_number']]);            

            /*
            | @Update ticket approvals
            |-----------------------------------------------*/
            $employee->ticket_approvals()->update(['technician_id' => $data['employee_number']]);

            /*
            | @Update event approvals
            |-----------------------------------------------*/
            $employee->event_approvals()->update(['approver_id' => $data['employee_number']]);

            /*
            | @Update learning approvals
            |-----------------------------------------------*/
            $employee->learning_approvals()->update(['approver_id' => $data['employee_number']]);

            /*
            | @Update tickets
            |-----------------------------------------------*/
            $employee->tickets()->update(['employee_id' => $data['employee_number']]);

            /*
            | @Update ticket updates
            |-----------------------------------------------*/
            $employee->ticket_updates()->update(['employee_id' => $data['employee_number']]);

            /*
            | @Update form template approvals
            |-----------------------------------------------*/
            $employee->form_template_approvals()->update(['employee_id' => $data['employee_number']]);            

            /*
            | @Update subordinates
            |-----------------------------------------------*/
            $employee->subordinates()->update(['supervisor_id' => $data['employee_number']]);
        }        

        /* Update employee */
        $employee->update($vars);


        /* Update many to many relationship if id has changed */
        if($hasIDChanged) {

            $employee->groups()->sync($relatedGroupIDs);
            $employee->assignees()->sync($relatedAssigneeIDs);
        }
    }

    /**
     * Update the users pending requests
     *
     */
    public function updateRequests($employee, $data) {

        /* Check if supervisor changes */
        if($employee->supervisor && $employee->supervisor->id != $data['supervisor_employee_number']) {

            $approverID = $data['supervisor_employee_number'];

            /* Update supervisor */
            $employee->updateSupervisor($approverID);                    
        }
    }

    /**
     * Update the users organization flow
     *
     */
    public function updateOrganization($employee, $data, $key) {

    	/* Set needed variables */
    	$company = null;
    	$division = null;
    	$department = null;
    	$team = null;
    	$position = null;


    	/* Check if company needs to be updated */
        $company = Company::where('name', $data['company'])->get()->first();

    	if(!$company && (strtolower($company->name) != strtolower($data['company']))) {

    		$company = Company::firstOrCreate([
								'name' => $data['company'],
						    	'creator_id' => $this->user->id,
						    	'updater_id' => $this->user->id,
							]);
    	}
       

        /* Check if there is a division data */
        if(!$data['group'])
            return $this->logs['errors'][] = "* Line " . ($key + 1) . ": Cannot assign user if the group column is empty" . "\n";

    	/* Check if division exists on the company */
    	$division = $company->divisions()->where([
								    		['company_id', $company->id],
								    		['name', $data['group']]
								    	])->get()->first();

    	/* Check if it division exists */
    	if(!$division)
			$division = Division::create([
							'company_id' => $company->id,
							'name' => $data['group'],
					    	'creator_id' => $this->user->id,
					    	'updater_id' => $this->user->id,
						]);


        /* Check if there is a department data */
        if(!$data['department'])
            return $this->logs['errors'][] = "* Line " . ($key + 1) . ": Cannot assign user if the department column is empty" . "\n";

    	/* Check if department exists on the division */
    	$department = $division->departments()->where([
								    		['division_id', $division->id],
								    		['name', $data['department']]
								    	])->get()->first();

    	/* Check if it department exists */
    	if(!$department)
			$department = Department::create([
							'division_id' => $division->id,
							'name' => $data['department'],
					    	'creator_id' => $this->user->id,
					    	'updater_id' => $this->user->id,
						]);


    	/* Check if team exists on the department */
    	$team = $department->teams()->where([
								    		['department_id', $department->id],
								    		['name', $data['team']]
								    	])->get()->first();

    	/* Check if it team exists */
    	if(!$team && $data['team']) {
			$team = Team::create([
							'department_id' => $department->id,
							'name' => $data['team'],
					    	'creator_id' => $this->user->id,
					    	'updater_id' => $this->user->id,
						]);        
        }


    	/* Check if position exists on the department */
    	$position = $department->positions()->where([
								    		['department_id', $department->id],
								    		['title', $data['position']]
								    	])->get()->first();

    	/* Check if it position exists */
    	if(!$position && $data['position']) {
			$position = Position::create([
							'department_id' => $department->id,
							'title' => $data['position'],
					    	'creator_id' => $this->user->id,
					    	'updater_id' => $this->user->id,
						]);
        }


		/* Update employee organization */
		$employee->assignDepartment(
            $department->id,
            $data['position'] ? $position->id : null, 
            $data['team'] ? $team->id : null
        );


        /* Check if the group var exists */
        if($company && $data['group_head']) {
            $divisions = Division::where([
                                    ['company_id', $company->id],
                                    ['name', $data['group_head']]
                                ])->get()->first();

            /* Check if groups exists */
            if($divisions->get()->count() > 0) {

                /* Assign employee to group */
                $divisions->assignGroupHead(['group_head_id' => $employee->id]);

            } else {

                $this->logs['errors'][] = "* Line " . ($key + 2) . ": Cannot assign as group head on " . $group . " cause the group doesn't exist. \n";
            }
        }
    }
}
