<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User;
use App\Team;
use App\Department;
use App\Division;
use App\Company;

class TeamFetchController extends Controller
{
    protected $user = null;
    protected $companies = [];

    protected $where = [];
    protected $orWhere = [];

    protected $whereInCol = 'id';
    protected $whereIn = [];

    protected $orWhereInCol = 'id';
    protected $orWhereIn = [];

    protected $whereNotInCol = 'id';
    protected $whereNotIn = [];

    protected $sort = 'created_at';
    protected $sortDir = 'asc';
    protected $search = '';


    /**
     * Fetch the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function fetch(Request $request)
    {
        /* Get default variable */
        $this->user = \Auth::user();        

        /* Set default filter/search param */
        $this->setParameters($request);


        /* Query */
        $teams = $this->fetchQuery($this->where, $this->sort);


        /* Do the pagination */
        $pagination = $teams ? $teams->paginate(10) : array_merge($teams, ['data' => []]);

        return response()->json([
            'lists' => $pagination,
        ]);
    }

    /**
     * Fetch query
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function setParameters($request) {

        /* Set query variables */
        if($request->has('sort'))
            $this->sort = $request->sort;


        /* Set Where */
        //


        /* Set WhereIn */
        if($request->has('search')) {
            array_push($this->whereIn, [
                'column' => 'id',
                'array' => Team::search($request->search)->get()->pluck('id')->toArray()
            ]);
        }

        if($request->has('company')) { 
            $company = Company::withTrashed()->findOrFail($request->company);
            $array = [];

            foreach ($company->divisions as $division) {
                foreach ($division->departments as $department) {

                    /* Merge the team IDs */
                    $array = array_merge($array, $department->teams()->pluck('id')->toArray());
                }
            }

            array_push($this->whereIn, [
                'column' => 'id',
                'array' => $array
            ]);            
        }

        if($request->has('division')) {
            $division = Division::withTrashed()->findOrFail($request->division);
            $array = [];

            foreach ($division->departments as $department) {

                /* Merge the team IDs */
                $array = array_merge($array, $department->teams()->pluck('id')->toArray());
            }

            array_push($this->whereIn, [
                'column' => 'id',
                'array' => $array
            ]);            
        }

        if($request->has('department')) {
            array_push($this->whereIn, [
                'column' => 'id',
                'array' => Department::findOrFail($request->department)->teams()->pluck('id')->toArray()
            ]);
        }


        /* Set WhereNotIn */
        if($request->has('xdepartment')) {
            array_push($this->whereNotIn, [
                'column' => 'id',
                'array' => Department::findOrFail($request->xdepartment)->teams()->pluck('id')->toArray()
            ]);            
        }


        /* Set Sort */
        if($request->has('sort')) {
            $sort = $request->input('sort');

            switch ($request->input('sort')) {
                case 'name':
                    $this->sort = $sort;
                break;
            }

            /* Set order by */
            if($request->has('order'))
                $this->sortDir = $request->input('order');
        }         


        /* Fetch included teams depending on the current users permission */
        $this->getTeams();       
    }

    /**
     * Fetch query
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    private function fetchQuery($where)
    {
        /* Select all Users */
        $select = Team::select(Team::TABLE_COLUMNS);


        /* Check if there is a whereIn clause */
        foreach ($this->whereIn as $key => $whereIn) {
            $select->whereIn($whereIn['column'], $whereIn['array']);
        }

        foreach ($this->orWhereIn as $key => $orWhereIn) {
            $select->orWhereIn($orWhereIn['column'], $orWhereIn['array']);
        }

        foreach ($this->whereNotIn as $key => $whereNotIn) {
            $select->whereNotIn($whereNotIn['column'], $whereNotIn['array']);
        }


        /* Check if there is a where clause */
        if($where)
            $select->where($where);

        if($this->orWhere)
            $select->orWhere($this->orWhere);

        return $select->with($this->getTableWiths())
                        ->orderBy($this->sort, $this->sortDir);
    }

    /**
     * Array of datas needed for the team table list
     */
    public function getTableWiths() {
        return [
            'department' => function($query) {
                $query->select(Department::MINIMAL_COLUMNS);
            },
        ];
    }  

    /**
     * Fetches all teams that is handled by the current user
     */    
    public function getTeams() {

        /* Fetch all included team IDs */
        $teamIDs = $this->getIncludedTeamIDs();


        /* Set orWhereIn variables */
        array_push($this->whereIn, [
            'column' => 'id',
            'array' => $teamIDs
        ]);
    }

    /**
     * Fetches all teams IDs handled by the user's role
     */ 
    protected function getIncludedTeamIDs() {

        /* Set necessary data */
        $companies = $this->getCompanyIDs($this->user, 'Adding/Editing of Teams');        
        $ids = [];

        foreach($companies as $key => $tmpCompany) {

            /* Check if role is null, meaning all companies */
            if(!$tmpCompany)
                return Team::get()->pluck('id')->toArray();


            /* Fetch company */
            $company = Company::withTrashed()->findOrFail($tmpCompany);


            foreach($company->divisions as $division) {
                foreach($division->departments as $department) {

                    /* Merge the team IDs */
                    $ids = array_merge($ids, $department->teams()->pluck('id')->toArray());
                }
            }
        }

        return $ids;
    }  

    /**
     * Fetch all company IDs the current user has permission to 
     *
     * @return array
     */
    protected function getCompanyIDs($user, $role) {
        $companies = [];

        foreach ($user->groups as $key => $group) {
            
            if($group->hasRole($role))
                array_push($companies, $group->company_id);
        }

        return $companies;
    }    
}
