<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User;
use App\Company;
use App\Division;
use App\Department;

class DepartmentFetchController extends Controller
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
        $departments = $this->fetchQuery($this->where);            


        /* Do the pagination */
        $pagination = $departments ? $departments->paginate(10) : array_merge($departments, ['data' => []]);

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
        if($request->has('group'))
            array_push($this->where, ['division_id', $request->group]);


        /* Set WhereIn */
        if($request->has('search')) {
            array_push($this->whereIn, [
                'column' => 'id',
                'array' => Department::search($request->search)->get()->pluck('id')->toArray()
            ]);
        }

        if($request->has('company')) {
            $company = Company::withTrashed()->findOrFail($request->company);
            $array = [];

            foreach ($company->divisions as $division) {

                /* Merge the department IDs */
                $array = array_merge($array, $division->departments()->pluck('id')->toArray());
            }

            array_push($this->whereIn, [
                'column' => 'id',
                'array' => $array
            ]);             
        }

        if($request->has('division')) {
            $division = Division::withTrashed()->findOrFail($request->division);
            $array = [];

            /* Merge the department IDs */
            array_push($this->whereIn, [
                'column' => 'id',
                'array' => $division->departments()->pluck('id')->toArray()
            ]);            
        }


        /* Set WhereNotIn */
        if($request->has('xdivision')) {
            array_push($this->whereNotIn, [
                'column' => 'id',
                'array' => Division::withTrashed()->findOrFail($request->xdivision)->departments()->pluck('id')->toArray()
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


        /* Fetch included departments depending on the current users permission */
        $this->getDepartments();       
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
        $select = Department::select(Department::TABLE_COLUMNS);


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
     * Array of datas needed for the employee table list
     */
    public function getTableWiths() {
        return [     
            'division' => function($query) {
                $query->select(Division::MINIMAL_COLUMNS);
            },
        ];
    }     

    /**
     * Fetches all departments that is handled by the current user
     */    
    public function getDepartments() {

        /* Fetch all included employee IDs */
        $departmentIDs = $this->getIncludedDepartmentIDs();


        /* Set orWhereIn variables */
        array_push($this->whereIn, [
            'column' => 'id',
            'array' => $departmentIDs
        ]);
    }

    /**
     * Fetches all departments IDs handled by the user's role
     */ 
    protected function getIncludedDepartmentIDs() {

        /* Set necessary data */
        $companies = $this->getCompanyIDs($this->user, 'Adding/Editing of Department');        
        $ids = [];

        foreach($companies as $key => $tmpCompany) {

            /* Check if role is null, meaning all companies */
            if(!$tmpCompany)
                return Department::get()->pluck('id')->toArray();


            /* Fetch company */
            $company = Company::withTrashed()->findOrFail($tmpCompany);


            foreach($company->divisions as $division) {

                /* Merge the department IDs */
                $ids = array_merge($ids, $division->departments()->pluck('id')->toArray());
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

    /**
     * Fetch the available positions & teams for the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function fetchPositionsTeams(Request $request, $id)
    {
        $department = Department::findOrFail($id);


        $positions = $department->positions()->select(['id', 'title'])->get();
        $teams = $department->teams()->select(['id', 'name'])->get();

        return response()->json([
            'positions' => $positions,
            'teams' => $teams,
        ]);     
    }

    /**
     * Fetch the available positions for the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function fetchPositions(Request $request, $id)
    {
    	$department = Department::findOrFail($id);


    	$positions = $department->positions()->select(['id', 'name'])->get();

        return response()->json([
            'lists' => $positions,
        ]);    	
    }

    /**
     * Fetch the available teams for the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function fetchTeams(Request $request, $id)
    {
    	$department = Department::findOrFail($id);


    	$teams = $department->teams()->select(['id', 'name'])->get();

        return response()->json([
            'lists' => $teams,
        ]);    	
    }    
}
