<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests\Departments\DepartmentStorePost;
use App\Http\Requests\Departments\DepartmentUpdateEmployeePost;
use App\Http\Requests\Departments\DepartmentUpdatePositionPost;
use App\Http\Requests\Departments\DepartmentUpdateTeamPost;

use App\Department;
use App\Division;

class DepartmentController extends Controller
{
    /**
     * Instantiate a new DepartmentController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('App\Http\Middleware\Departments\DepartmentIndexMiddleware', ['only' => ['index']]);

        $this->middleware('App\Http\Middleware\Departments\ViewDepartmentMiddleware', ['only' => ['show', 'update', 'addEmployees', 'removeEmployees', 'addPositions', 'removePositions']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('pages.departments.departments');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = \Auth::user();

        $companies = $user->getHandledCompanies('Adding/Editing of Department');


        return view('pages.departments.createdepartment', [
            'companies' => $companies,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DepartmentStorePost $request)
    {
        $vars = $request->all();
        $vars['creator_id'] = \Auth::user()->id;
        $vars['updater_id'] = \Auth::user()->id;


        /* Create the department */
        $department = Department::create($vars);

        return response()->json([
            'response' => 1,
            'redirectURL' => route('department.show', $department->id),
            'title' => 'Create department',
            'message' => 'Successfully created department ' . $department->name
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
        $user = \Auth::user();
        $department = Department::findOrFail($id);

        $companies = $user->getHandledCompanies('Adding/Editing of Department');


        return view('pages.departments.showdepartment', [
            'department' => $department,
            'companies' => $companies,
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
    public function update(DepartmentStorePost $request, $id)
    {
        $department = Department::findOrFail($id);
        
        $vars = $request->all();
        $vars['updater_id'] = \Auth::user()->id;

        
        /* Update the department */
        $department->update($vars);

        return response()->json([
            'response' => 1,
            'title' => 'Update department details',
            'message' => 'Successfully updated department ' . $department->name
        ]);
    }

    /**
     * Update the specified resource's Employees in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function addEmployees(DepartmentUpdateEmployeePost $request, $id)
    {
        $department = Department::findOrFail($id);
        $employees = $request->input('employees');


        /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();


        /* Update the department employees */
        $department->addEmployees($employees);


        /*
        | @End Transaction
        |---------------------------------------------*/
        \DB::commit();


        return response()->json([
            'response' => 1,
            'message' => 'Successfully added ' . $employees->count() . ' employee(s) from department ' . $department->name
        ]);
    }

    /**
     * Remove the specified resource's Employee in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function removeEmployees(DepartmentUpdateEmployeePost $request, $id)
    {
        $department = Department::findOrFail($id);
        $employees = $request->input('employees');


        /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();


        /* Remove the department employees */
        $department->removeEmployees($employee);


        /*
        | @End Transaction
        |---------------------------------------------*/
        \DB::commit();


        return response()->json([
            'response' => 1,
            'message' => 'Successfully removed ' . $employees->count() . ' employee(s) from department ' . $department->name
        ]);
    }     

    /**
     * Add a Position to the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function addPositions(DepartmentUpdatePositionPost $request, $id)
    {
        $department = Department::findOrFail($id);
        $positions = $request->input('positions');


        /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();


        /* Add position to department */
        $department->addPositions($positions);


        /*
        | @End Transaction
        |---------------------------------------------*/
        \DB::commit();


        return response()->json([
            'response' => 1,
            'redirectURL' => route('department.show', $department->id),
            'title' => 'Add positions',
            'message' => 'Successfully added ' . count($positions) . ' position(s) to ' . $department->name
        ]);
    }

    /**
     * Remove a position to the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function removePositions(DepartmentUpdatePositionPost $request, $id)
    {
        $department = Department::findOrFail($id);
        $positions = $request->input('positions');


        /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();


        /* Remove positions to department */
        $department->removePositions($positions);


        /*
        | @End Transaction
        |---------------------------------------------*/
        \DB::commit();


        return response()->json([
            'response' => 1,
            'redirectURL' => route('department.show', $department->id),
            'title' => 'Remove positions',
            'message' => 'Successfully removed ' . count($positions) . ' position(s) to ' . $department->name
        ]);
    }  

    /**
     * Add a team to the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function addTeams(DepartmentUpdateTeamPost $request, $id)
    {
        $department = Department::findOrFail($id);
        $teams = $request->input('teams');


        /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();


        /* Add team to department */
        $department->addTeams($teams);


        /*
        | @End Transaction
        |---------------------------------------------*/
        \DB::commit();


        return response()->json([
            'response' => 1,
            'redirectURL' => route('department.show', $department->id),
            'title' => 'Add teams',
            'message' => 'Successfully added ' . count($teams) . ' team(s) to ' . $department->name
        ]);
    }

    /**
     * Remove a team to the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function removeTeams(DepartmentUpdateTeamPost $request, $id)
    {
        $department = Department::findOrFail($id);
        $teams = $request->input('teams');


        /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();


        /* Remove teams to department */
        $department->removeTeams($teams);


        /*
        | @End Transaction
        |---------------------------------------------*/
        \DB::commit();
        

        return response()->json([
            'response' => 1,
            'redirectURL' => route('department.show', $department->id),
            'title' => 'Remove teams',
            'message' => 'Successfully removed ' . count($teams) . ' team(s) to ' . $department->name
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
