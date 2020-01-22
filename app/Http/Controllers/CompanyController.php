<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests\Companies\CompanyStorePost;
use App\Http\Requests\Companies\CompanyUpdateDivisionPost;

use App\Company;
use App\Location;

class CompanyController extends Controller
{
    /**
     * Instantiate a new CompanyController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('App\Http\Middleware\Companies\CompanyIndexMiddleware', ['only' => ['index']]);

        $this->middleware('App\Http\Middleware\Companies\ViewCompanyMiddleware', ['only' => ['show', 'update', 'addDivisions', 'removeDivisions', 'archive', 'restore']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('pages.companies.companies');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    { 
        return view('pages.companies.createcompany');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CompanyStorePost $request)
    {
        $vars = $request->except(['abbreviation']);
        $vars['abbreviation'] = ucwords($request->input('abbreviation'));
        $vars['creator_id'] = \Auth::user()->id;
        $vars['updater_id'] = \Auth::user()->id;


        /* Create the company */
        $company = Company::create($vars);

        return response()->json([
            'response' => 1,
            'redirectURL' => route('company.show', $company->id),
            'title' => 'Create company',
            'message' => 'Successfully created company ' . $company->name
        ]);
    }

    /**
     * Add a division to the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function addDivisions(CompanyUpdateDivisionPost $request, $id)
    {
        $company = Company::withTrashed()->findOrFail($id);
        $divisions = $request->input('divisions');


        /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();


        /* Add divisions to company */
        $company->addDivisions($divisions);


        /*
        | @End Transaction
        |---------------------------------------------*/
        \DB::commit();


        return response()->json([
            'response' => 1,
            'redirectURL' => route('company.show', $company->id),
            'title' => 'Add Groups',
            'message' => 'Successfully added ' . count($divisions) . ' groups(s) to ' . $company->renderAbbr()
        ]);
    }

    /**
     * Remove a division to the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function removeDivisions(CompanyUpdateDivisionPost $request, $id)
    {
        $company = Company::withTrashed()->findOrFail($id);
        $divisions = $request->input('divisions');


        /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();


        /* Remove divisions to company */
        $company->removeDivisions($divisions);


        /*
        | @End Transaction
        |---------------------------------------------*/
        \DB::commit();


        return response()->json([
            'response' => 1,
            'redirectURL' => route('company.show', $company->id),
            'title' => 'Remove Groups',
            'message' => 'Successfully removed ' . count($divisions) . ' groups(s) to ' . $company->renderAbbr()
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
        $company = Company::withTrashed()->findOrFail($id);
        $locations = Location::select(Location::MINIMAL_COLUMNS)->get();


        return view('pages.companies.showcompany', [
            'company' => $company,
            'locations' => $locations,
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
    public function update(CompanyStorePost $request, $id)
    {
        $company = Company::withTrashed()->findOrFail($id);
        
        $vars = $request->except(['abbreviation']);
        $vars['abbreviation'] = ucwords($request->input('abbreviation'));
        $vars['updater_id'] = \Auth::user()->id;


        /* Update the company */
        $company->update($vars);

        return response()->json([
            'response' => 1,
            'title' => 'Update company details',
            'message' => 'Successfully updated company ' . $company->renderAbbr()
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
        $company = Company::findOrFail($id);


        /* Soft delete company */
        $company->delete();

        return response()->json([
            'response' => 1,
            'redirectURL' => route('company.show', $company->id),
            'title' => 'Archive Company',
            'message' => 'Successfully archived ' . $company->name
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
        $company = Company::onlyTrashed()->findOrFail($id);


        /* Restore company */
        $company->restore();

        return response()->json([
            'response' => 1,
            'redirectURL' => route('company.show', $company->id),
            'title' => 'Restore Company',
            'message' => 'Successfully restored ' . $company->name
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
