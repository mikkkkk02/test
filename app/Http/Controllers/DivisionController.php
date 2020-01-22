<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests\Divisions\DivisionStorePost;
use App\Http\Requests\Divisions\DivisionAssignCompanyPost;
use App\Http\Requests\Divisions\DivisionUpdateDepartmentPost;

use App\Notifications\Requests\RequestHasApprover;

use App\Division;
use App\Company;
use App\User;
use App\FormApprover;
use App\FormTemplateApprover;

class DivisionController extends Controller
{
    /**
     * Instantiate a new DivisionController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('App\Http\Middleware\Divisions\DivisionIndexMiddleware', ['only' => ['index']]);

        $this->middleware('App\Http\Middleware\Divisions\ViewDivisionMiddleware', ['only' => ['show', 'update', 'assignCompany', 'removeCompany', 'addDepartments', 'removeDepartments', 'restore', 'archive']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('pages.divisions.divisions');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = \Auth::user();

        $companies = $user->getHandledCompanies('Adding/Editing of Group');
        $employees = User::select(User::MINIMAL_COLUMNS)->orderBy('last_name', 'asc')->get();


        return view('pages.divisions.createdivision', [
            'companies' => $companies,
            'employees' => $employees,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DivisionStorePost $request)
    {
        $vars = $request->all();
        $vars['creator_id'] = \Auth::user()->id;
        $vars['updater_id'] = \Auth::user()->id;


        /* Create the division */
        $division = Division::create($vars);

        return response()->json([
            'response' => 1,
            'redirectURL' => route('division.show', $division),
            'title' => 'Create group',
            'message' => 'Successfully created group ' . $division->name
        ]); 
    }

    /**
     * Assign company to the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function assignCompany(DivisionAssignCompanyPost $request, $id)
    {
        $division = Division::withTrashed()->findOrFail($id);
        $company = Company::findOrFail($request->input('company_id'));


        /* Add division to division */
        $division->assignCompany($company);

        return response()->json([
            'response' => 1,
            'message' => 'Successfully assigned ' . $division->name . ' to ' . $company->renderAbbr()
        ]);
    }

    /**
     * Unassign company to the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function removeCompany(DivisionAssignCompanyPost $request, $id)
    {
        $division = Division::withTrashed()->findOrFail($id);
        $company = Company::findOrFail($request->input('company_id'));


        /* Remove division to company */
        $division->unassignCompany();

        return response()->json([
            'response' => 1,
            'message' => 'Successfully unassigned ' . $division->name . ' to ' . $company->renderAbbr()
        ]);
    }

    /**
     * Add a Department to the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function addDepartments(DivisionUpdateDepartmentPost $request, $id)
    {
        $division = Division::withTrashed()->findOrFail($id);
        $departments = $request->input('departments');


        /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();


        /* Add department to division */
        $division->addDepartments($departments);


        /*
        | @End Transaction
        |---------------------------------------------*/
        \DB::commit();


        return response()->json([
            'response' => 1,
            'redirectURL' => route('division.show', $division->id),
            'title' => 'Add Departments',
            'message' => 'Successfully added ' . count($departments) . ' department(s) to ' . $division->name
        ]);
    }

    /**
     * Remove a Department to the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function removeDepartments(DivisionUpdateDepartmentPost $request, $id)
    {
        $division = division::withTrashed()->findOrFail($id);
        $departments = $request->input('departments');


        /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();


        /* Remove departments to division */
        $division->removedepartments($departments);


        /*
        | @End Transaction
        |---------------------------------------------*/
        \DB::commit();
        

        return response()->json([
            'response' => 1,
            'redirectURL' => route('division.show', $division->id),
            'title' => 'Remove Groups',
            'message' => 'Successfully removed ' . count($departments) . ' department(s) to ' . $division->name
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
        $division = Division::withTrashed()->findOrFail($id);

        $companies = $user->getHandledCompanies('Adding/Editing of Group');
        $employees = User::select(User::MINIMAL_COLUMNS)->orderBy('last_name', 'asc')->get();


        return view('pages.divisions.showdivision', [
            'division' => $division,
            'companies' => $companies,
            'employees' => $employees,
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
    public function update(DivisionStorePost $request, $id)
    {
        $division = Division::withTrashed()->findOrFail($id);
        
        $vars = $request->except(['group_head_id']);
        $vars['updater_id'] = \Auth::user()->id;


        /* Update group head */
        $division->assignGroupHead($request->input('group_head_id'));

        /* Update the division */
        $division->update($vars);

        return response()->json([
            'response' => 1,
            'title' => 'Update group details',
            'message' => 'Successfully updated group ' . $division->name
        ]);    
    }     

    /**
     * Archive the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function archive($id)
    {
        $division = Division::findOrFail($id);


        /* Soft delete division */
        $division->delete();

        return response()->json([
            'response' => 1,
            'redirectURL' => route('division.show', $division->id),
            'title' => 'Archive Division',
            'message' => 'Successfully archived ' . $division->name
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
        $division = division::onlyTrashed()->findOrFail($id);


        /* Restore division */
        $division->restore();

        return response()->json([
            'response' => 1,
            'redirectURL' => route('division.show', $division->id),
            'title' => 'Restore Division',
            'message' => 'Successfully restored ' . $division->name
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

