<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests\Teams\TeamStorePost;
use App\Http\Requests\Teams\TeamUpdateEmployeePost;

use App\Team;
use App\Department;

class TeamController extends Controller
{
    /**
     * Instantiate a new TeamController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('App\Http\Middleware\Teams\TeamIndexMiddleware', ['only' => ['index', 'create']]);

        $this->middleware('App\Http\Middleware\Teams\ViewTeamMiddleware', ['only' => ['show', 'update', 'addEmployees', 'removeEmployees']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('pages.teams.teams');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = \Auth::user();

        $companies = $user->getHandledCompanies('Adding/Editing of Teams');


        return view('pages.teams.createteam', [
            'companies' => $companies,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TeamStorePost $request)
    {
        $vars = $request->all();
        $vars['creator_id'] = \Auth::user()->id;
        $vars['updater_id'] = \Auth::user()->id;


        /* Create the team */
        $team = Team::create($vars);

        return response()->json([
            'response' => 1,
            'redirectURL' => route('team.show', $team->id),
            'title' => 'Create team',
            'message' => 'Successfully created Team ' . $team->name
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
        $team = Team::findOrFail($id);

        $companies = $user->getHandledCompanies('Adding/Editing of Teams');


        return view('pages.teams.showteam', [
            'team' => $team,
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
    public function update(TeamStorePost $request, $id)
    {
        $team = Team::findOrFail($id);
        
        $vars = $request->all();
        $vars['updater_id'] = \Auth::user()->id;
        

        /* Update the team */
        $team->update($vars);

        return response()->json([
            'response' => 1,
            'title' => 'Update team details',
            'message' => 'Successfully updated team ' . $team->name
        ]);
    }

    /**
     * Update the specified resource's Employees in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function addEmployees(TeamUpdateEmployeePost $request, $id)
    {
        $team = Team::findOrFail($id);
        $employees = $request->input('users');


        /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();


        /* Update the team employees */
        $team->addEmployees($employees);


        /*
        | @End Transaction
        |---------------------------------------------*/
        \DB::commit();


        return response()->json([
            'response' => 1,
            'redirectURL' => route('team.show', $team->id),
            'title' => 'Add employees',
            'message' => 'Successfully added ' . count($employees) . ' employee(s) from team ' . $team->name
        ]);
    }

    /**
     * Remove the specified resource's Employee in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function removeEmployees(TeamUpdateEmployeePost $request, $id)
    {
        $team = Team::findOrFail($id);
        $employees = $request->input('users');


        /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();


        /* Remove the team employees */
        $team->removeEmployees($employees);


        /*
        | @End Transaction
        |---------------------------------------------*/
        \DB::commit();


        return response()->json([
            'response' => 1,
            'redirectURL' => route('team.show', $team->id),
            'title' => 'Remove employees',
            'message' => 'Successfully removed ' . count($employees) . ' employee(s) from team ' . $team->name
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
