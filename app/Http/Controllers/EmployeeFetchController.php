<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User;
use App\Group;
use App\EmployeeCategory;
use App\Company;
use App\Location;
use App\Division;
use App\Department;
use App\DepartmentEmployee;
use App\Position;
use App\Team;

use App\Event;
use App\EventParticipant;

class EmployeeFetchController extends Controller
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

    protected $sort = 'last_name';
    protected $sortDir = 'asc';    
    protected $search = '';

    protected $limit = 10;


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
        $users = $this->fetchQuery($request, $this->where);            


        /* Do the pagination */
        $pagination = $users ? $users->paginate($this->limit) : array_merge($users, ['data' => []]);

        if ($request->has('all') && config('app.debug') && config('app.env') == 'Local') {
            $pagination = User::select(User::MINIMAL_COLUMNS)->orderBy('last_name', 'asc')->get();
        }

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

        if($request->has('limit'))
            $this->limit = $request->limit;        


        /* Set Where */
        if($request->has('category'))
            array_push($this->where, ['employee_category_id', $request->category]);


        /* Set WhereIn */
        if($request->has('search')) {
            array_push($this->whereIn, [
                'column' => 'id',
                'array' => User::search($request->search)->get()->pluck('id')->toArray()
            ]);
        }

        if($request->has('archive')) {
            array_push($this->whereIn, [
                'column' => 'id',
                'array' => User::onlyTrashed()->get()->pluck('id')->toArray()
            ]);
        }        

        if($request->has('group')) {
            array_push($this->whereIn, [
                'column' => 'id',
                'array' => Group::withTrashed()->findOrFail($request->group)->users()->pluck('id')->toArray()
            ]);            
        }

        if($request->has('company')) { 
            $company = Company::withTrashed()->findOrFail($request->company);
            $array = [];

            foreach ($company->divisions as $division) {
                foreach ($division->departments as $department) {

                    /* Merge the employee IDs */
                    $array = array_merge($array, $department->employees()->pluck('employee_id')->toArray());
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

                /* Merge the employee IDs */
                $array = array_merge($array, $department->employees()->pluck('employee_id')->toArray());
            }

            array_push($this->whereIn, [
                'column' => 'id',
                'array' => $array
            ]);             
        }

        if($request->has('department')) {
            array_push($this->whereIn, [
                'column' => 'id',
                'array' => Department::findOrFail($request->department)->employees()->pluck('employee_id')->toArray()
            ]);
        }

        if($request->has('position')) {
            array_push($this->whereIn, [
                'column' => 'id',
                'array' => Position::findOrFail($request->position)->employees()->pluck('employee_id')->toArray()
            ]);             
        }

        if($request->has('team')) {
            array_push($this->whereIn, [
                'column' => 'id',
                'array' => Team::findOrFail($request->team)->employees()->pluck('employee_id')->toArray()
            ]);             
        }


        /* Set WhereNotIn */
        if($request->has('xgroup')) {
            array_push($this->whereNotIn, [
                'column' => 'id',
                'array' => Group::withTrashed()->findOrFail($request->xgroup)->users()->pluck('id')->toArray()
            ]); 
        }

        if($request->has('xposition')) {
            array_push($this->whereNotIn, [
                'column' => 'id',
                'array' => Position::findOrFail($request->xposition)->employees()->pluck('employee_id')->toArray()
            ]);
        }

        if($request->has('xteam')) {
            array_push($this->whereNotIn, [
                'column' => 'id',
                'array' => Team::findOrFail($request->xteam)->employees()->pluck('employee_id')->toArray()
            ]);
        }        


        /* Set Sort */
        if($request->has('sort')) {
            $sort = $request->input('sort');

            switch ($request->input('sort')) {
                case 'id': case 'email': case 'last_name':
                    $this->sort = $sort;
                break;
            }

            /* Set order by */
            if($request->has('order'))
                $this->sortDir = $request->input('order');
        }        


        /* Fetch included employees depending on the current users permission */
        $this->getEmployees();
    }

    /**
     * Fetch query
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    private function fetchQuery($request, $where)
    {
        /* Select all Users */
        $select = $request->has('archive') ? User::withTrashed()->select(User::TABLE_COLUMNS) : User::select(User::TABLE_COLUMNS);


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
            'category' => function($query) {
                $query->select(EmployeeCategory::MINIMAL_COLUMNS);
            },                        
            'supervisor' => function($query) {
                $query->select(User::MINIMAL_COLUMNS);
            },
            'location' => function($query) {
                $query->select(Location::MINIMAL_COLUMNS);
            },             
            'department' => function($query) {
                $query->select(DepartmentEmployee::MINIMAL_COLUMNS);
            },
            'department.team' => function($query) {
                $query->select(Team::MINIMAL_COLUMNS);
            },                        
            'department.position' => function($query) {
                $query->select(Position::MINIMAL_COLUMNS);
            },                        
            'department.department' => function($query) {
                $query->select(Department::MINIMAL_COLUMNS);
            },
            'department.department.division' => function($query) {
                $query->select(Division::MINIMAL_COLUMNS);
            },
            'department.department.division.company'  => function($query) {
                $query->select(Company::MINIMAL_COLUMNS);
            },
        ];
    }   

    /**
     * Fetches all employees that is handled by the current user
     */    
    public function getEmployees() {

        /* Fetch all included employee IDs */
        $employeeIDs = $this->getIncludedEmployeeIDs();


        /* Set orWhereIn variables */
        array_push($this->whereIn, [
            'column' => 'id',
            'array' => $employeeIDs
        ]);
    }

    /**
     * Fetches all employee IDs handled by the user's role
     */ 
    protected function getIncludedEmployeeIDs() {

        /* Set necessary data */
        $companies = $this->getCompanyIDs($this->user, 'Adding/Editing of Employee Profile');        
        $ids = [];

        foreach($companies as $key => $tmpCompany) {

            /* Check if role is null, meaning all companies */
            if(!$tmpCompany)
                return User::withTrashed()->get()->pluck('id')->toArray();


            /* Fetch company */
            $company = Company::withTrashed()->findOrFail($tmpCompany);


            foreach($company->divisions as $division) {
                foreach($division->departments as $department) {

                    /* Merge the employee IDs */
                    $ids = array_merge($ids, $department->employees()->pluck('employee_id')->toArray());
                }
            }
        }

        /* Include self */
        $ids = array_merge($ids, [$this->user->id]);
        

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
