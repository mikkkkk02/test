<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests\Positions\PositionStorePost;
use App\Http\Requests\Positions\PositionUpdateEmployeePost;

use App\Position;
use App\Department;

class PositionController extends Controller
{
    /**
     * Instantiate a new PositionController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('App\Http\Middleware\Positions\ViewPositionMiddleware', ['only' => ['show', 'update', 'addEmployees', 'removeEmployees']]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = \Auth::user();

        $companies = $user->getHandledCompanies('Adding/Editing of Positions');


        return view('pages.departments.createposition', [
            'companies' => $companies,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PositionStorePost $request)
    {
        $vars = $request->all();
        $vars['creator_id'] = \Auth::user()->id;
        $vars['updater_id'] = \Auth::user()->id;


        /* Create the position */
        $position = Position::create($vars);

        return response()->json([
            'response' => 1,
            'redirectURL' => route('position.show', $position->id),
            'title' => 'Create position',
            'message' => 'Successfully created position ' . $position->name
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
        $position = Position::findOrFail($id);

        $companies = $user->getHandledCompanies('Adding/Editing of Positions');        


        return view('pages.departments.showposition', [
            'position' => $position,
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
    public function update(PositionStorePost $request, $id)
    {
        $position = Position::findOrFail($id);        
        $vars = $request->all();
        $vars['updater_id'] = \Auth::user()->id;


        /* Update the position */
        $position->update($vars);

        return response()->json([
            'response' => 1,
            'title' => 'Update position details',
            'message' => 'Successfully updated position ' . $position->name
        ]);
    }

    /**
     * Update the specified resource's Employees in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function addEmployees(PositionUpdateEmployeePost $request, $id)
    {
        $position = Position::findOrFail($id);
        $employees = $request->input('users');


        /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();


        /* Update the position employees */
        $position->addEmployees($employees);


        /*
        | @End Transaction
        |---------------------------------------------*/
        \DB::commit();


        return response()->json([
            'response' => 1,
            'redirectURL' => route('position.show', $position->id),
            'title' => 'Add employees',
            'message' => 'Successfully added ' . count($employees) . ' employee(s) from position ' . $position->title
        ]);
    }

    /**
     * Remove the specified resource's Employee in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function removeEmployees(PositionUpdateEmployeePost $request, $id)
    {
        $position = Position::findOrFail($id);
        $employees = $request->input('users');


        /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();


        /* Remove the employees */
        $position->removeEmployees($employees);


        /*
        | @End Transaction
        |---------------------------------------------*/
        \DB::commit();


        return response()->json([
            'response' => 1,
            'redirectURL' => route('position.show', $position->id),
            'title' => 'Remove employees',
            'message' => 'Successfully removed ' . count($employees) . ' employee(s) from position ' . $position->title
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
