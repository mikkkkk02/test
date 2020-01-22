<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use App\Http\Requests\Employees\EmployeeStorePost;
use App\Http\Requests\Employees\EmployeeUpdateSettingsPost;
use App\Http\Requests\Employees\UploadAvatarStorePost;

use App\Helper\RouteChecker;

use App\User;
use App\Location;
use App\EmployeeCategory;
use App\Form;
use App\FormTemplate;
use App\FormTemplateCategory;
use App\Company;
use App\Group;
use App\EventParticipant;
use App\LearningParticipant;

class EmployeeController extends Controller
{
    /**
     * Instantiate a new EmployeeController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('App\Http\Middleware\Employees\EmployeeIndexMiddleware', ['only' => ['index']]);

        $this->middleware('App\Http\Middleware\Employees\ViewEmployeeMiddleware', ['only' => ['show', 'update', 'updateSettings', 'archive', 'restore']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $employeeFilters = User::renderTableFilter();


        return view('pages.employees.employees', [
            'employeeFilters' => json_encode($employeeFilters),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = \Auth::user();

        $companies = $user->getHandledCompanies('Adding/Editing of Employee Profile');
        $employees = User::select(User::MINIMAL_COLUMNS)->orderBy('last_name', 'asc')->get();
        $locations = Location::select(Location::MINIMAL_COLUMNS)->get();               
        $categories = EmployeeCategory::select(EmployeeCategory::MINIMAL_COLUMNS)->get();
        $groups = Group::getAvailableGroups('Adding/Editing of User Responsibilities/Groups');


        return view('pages.employees.createemployee', [
            'employees' => $employees,
            'locations' => $locations,
            'categories' => $categories,
            'companies' => $companies,
            'groups' => $groups,

            'canEdit' => 1,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(EmployeeStorePost $request)
    {
        $vars = $request->except(['department_id', 'position_id', 'team_id']);

        /* Create the employee */
        $employee = User::create($vars);

        /* Assign employee department */
        if($request->has('department_id') && $request->input('department_id')) {

            /* Assign to department */
            $employee->assignDepartment(
                $request->input('department_id'),
                $request->input('position_id'),
                $request->input('team_id')
            );
        }

        return response()->json([
            'response' => 1,
            'redirectURL' => route('employee.show', $employee->id),
            'title' => 'Register employee',
            'message' => 'Successfully created employee ' . $employee->renderFullname()
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $routeChecker = new RouteChecker(\Request::route());
        $user = \Auth::user();

        $employee = User::withTrashed()->with(User::TABLE_WITHS)->findOrFail($id);

        $companies = $user->getHandledCompanies('Adding/Editing of Employee Profile');        
        $employees = User::select(User::MINIMAL_COLUMNS)->where('id', '!=', $employee->id)->orderBy('last_name', 'asc')->get();
        $locations = Location::select(Location::MINIMAL_COLUMNS)->get();       
        $categories = EmployeeCategory::select(EmployeeCategory::MINIMAL_COLUMNS)->get();
        $groups = Group::getAvailableGroups('Adding/Editing of User Responsibilities/Groups');   

        /* Can Permission */
        $canEdit = $routeChecker->hasModuleRoles(['Adding/Editing of Employee Profile']);


        return view('pages.employees.showemployee', [
            'employee' => $employee,

            'employees' => $employees,
            'locations' => $locations,
            'categories' => $categories,
            'companies' => $companies,
            'groups' => $groups,

            'canEdit' => $canEdit,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(EmployeeStorePost $request, $id)
    {
        $employee = User::withTrashed()->findOrFail($id);
        $vars = $request->except([
                    'company_id', 'group_id', 'department_id', 
                    'team_id', 'position_id', 'supervisor_id',
                ]);


        /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();

        /* Update the employee */
        $employee->update($vars);

        /* Update the user's department, team, position & immediate leader */
        $this->updateUserGroups($request, $employee);

        /* Update supervisor */
        if($request->has('supervisor_id') && $request->input('supervisor_id') != $employee->supervisor_id)
            $employee->updateSupervisor($request->input('supervisor_id'));


        /*
        | @End Transaction
        |---------------------------------------------*/
        \DB::commit();


        return response()->json([
            'response' => 1,
            'title' => 'Update Employee details',
            'message' => 'Successfully updated employee ' . $employee->renderFullname()
        ]);
    }

    /**
     * Update the specified resource's groups in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function updateUserGroups(Request $request, $user)
    {
        /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();


        /* Update or create the department */
        if($user->department()->exists()) {

            /* Update the department */
            if($request->input('department_id') != $user->department->department_id)
                $user->department->setDepartment($request->input('department_id'));            

            /* Update the team */
            if($request->input('team_id') != $user->department->team_id)
                $user->department->setTeam($request->input('team_id'));

            /* Update the position */
            if($request->input('position_id') != $user->department->position_id)
                $user->department->setPosition($request->input('position_id'));

        } else {

            /* Check if department var is empty */
            if($request->has('department_id') && $request->input('department_id')) {

                /* Assign to department */
                $user->assignDepartment(
                    $request->input('department_id'),
                    $request->input('position_id'),
                    $request->input('team_id')
                );
            }
        }


        /*
        | @End Transaction
        |---------------------------------------------*/
        \DB::commit();
    }   

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateSettings(EmployeeUpdateSettingsPost $request, $id)
    {
        $employee = User::withTrashed()->findOrFail($id);
        $vars = $request->except(['onVacation']);

        $vars['onVacation'] = $request->has('onVacation') ? 1 : 0;


        /* Update the employee */
        $employee->update($vars);

        /* Update the user's assignee */
        if($request->has('hasAssignees'))
            $employee->updateAssignee($request->input('employees'));


        return response()->json([
            'response' => 1,
            'title' => 'Update Settings',
            'message' => 'Successfully updated employee ' . $employee->name . ' settings'
        ]);      
    }

    public function uploadAvatar(UploadAvatarStorePost $request, $id)
    {
        $employee = User::withTrashed()->findOrFail($id);
        $vars = $request->except(['profile_photo']);

        if($request->hasFile('profile_photo')) {
            if($employee->profile_photo) {
                Storage::delete('public/' . $employee->getProfilePhoto());
            }

            $vars['profile_photo'] = $request->file('profile_photo')->store('avatars', 'public');
        }

        $employee->update($vars);

        return redirect()->back();
    }

    /**
     * Archive the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function archive($id)
    {
        $employee = User::findOrFail($id);


        /* Soft delete group */
        $employee->delete();

        return response()->json([
            'response' => 1,
            'redirectURL' => route('employee.show', $employee->id),
            'title' => 'Archive Employee',
            'message' => 'Successfully archived ' . $employee->renderFullname()
        ]);
    }

    /**
     * Restore the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        $employee = User::onlyTrashed()->findOrFail($id);


        /* Restore group */
        $employee->restore();

        return response()->json([
            'response' => 1,
            'redirectURL' => route('employee.show', $employee->id),
            'title' => 'Restore Employee',
            'message' => 'Successfully restored ' . $employee->renderFullname()
        ]);
    }     

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
