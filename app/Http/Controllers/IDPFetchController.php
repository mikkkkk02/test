<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Idp;
use App\IdpApprover;
use App\IdpCompetency;
use App\User;
use App\Company;
use App\Division;
use App\Department;
use App\DepartmentEmployee;
use App\Team;
use App\Position;

class IDPFetchController extends Controller
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

    protected $sort = array(
                    ['column' => 'completion_year', 'direction' => 'desc'],
                    ['column' => 'status', 'direction' => 'asc'],
                    ['column' => 'created_at', 'direction' => 'desc'],
                );
    protected $search = '';


    /**
     * Fetch the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function fetch(Request $request, $export = false)
    {
        /* Get default variable */
        $this->user = \Auth::user();        

        /* Set default filter/search param */
        $this->setParameters($request);


        /* Query */
        $idps = $this->fetchQuery($this->where, $this->whereIn, $this->whereNotIn, $this->sort);            


        /* Check if this is for export */
        if($export)
            return $idps->get();


        /* Do the pagination */
        $pagination = $idps ? $idps->paginate(10) : array_merge($idps, ['data' => []]);


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
        if($request->has('user'))
            array_push($this->where, ['employee_id', $request->user]);

        if($request->has('employee'))
            array_push($this->where, ['employee_id', $request->employee]);

        if($request->has('status'))
            array_push($this->where, ['status', $request->status]);   

        if($request->has('year'))
            array_push($this->where, ['completion_year', $request->year]);

        if($request->has('learningtype'))
            array_push($this->where, ['learning_type', $request->learningtype]);

        if($request->has('competencytype'))
            array_push($this->where, ['competency_type', $request->competencytype]);        

        if($request->has('specificcompetency'))
            array_push($this->where, ['competency_id', $request->specificcompetency]);

        if($request->has('start') && $request->has('end')) {

            $start = $request->input('start');
            $end = $request->input('end');


            array_push($this->where, ['created_at', '>=', $start]);
            array_push($this->where, ['created_at', '<=', $end]);
        }



        /* Set WhereIn */
        if($request->has('search')) {
            array_push($this->whereIn, [
                'column' => 'id',
                'array' => Idp::search($request->search)->get()->pluck('id')->toArray()
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
                'column' => 'employee_id',
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
                'column' => 'employee_id',
                'array' => $array
            ]);            
        }  

        if($request->has('department')) {
            array_push($this->whereIn, [
                'column' => 'employee_id',
                'array' => Department::findOrFail($request->department)->employees()->pluck('employee_id')->toArray()
            ]);            
        }

        if($request->has('team')) {
            array_push($this->whereNotIn, [
                'column' => 'employee_id',
                'array' => Team::findOrFail($request->team)->employees()->pluck('employee_id')->toArray()
            ]);
        }

        if($request->has('myteam')) {
            array_push($this->whereIn, [
                'column' => 'employee_id',
                'array' => User::withTrashed()->find($request->myteam)->getSubordinateID()
            ]);            
        }

        if($request->has('approval')) {
            array_push($this->whereIn, [
                'column' => 'id',
                'array' => User::withTrashed()->findOrFail($request->approval)
                                    ->idp_approvals()
                                    ->join('idps', 'idp_id', '=', 'idps.id')
                                    ->select(['idp_approvers.status AS idp_approvers.status', 'idp_approvers.enabled AS idp_approvers.enabled', 'idp_id', 'idps.approval_status AS idp.approval_status'])
                                    ->where('idp_approvers.enabled', 1)
                                    ->where('idp_approvers.status', IdpApprover::PENDING)
                                    ->pluck('idp_id')
                                    ->toArray()
            ]);
        }          


        if(!$request->has('approval')) {

            /* Fetch included employees depending on the current users permission */
            $this->getEmployees();
        }

        // http_response_code(500); 
        // dd($this->where); 
        // dd($this->whereIn); 
        // dd($this->orWhereIn);        
    }

    /**
     * Fetch query
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    private function fetchQuery($where, $whereIn, $whereNotIn, $sort)
    {
        /* Select all Users */
        $select = Idp::select(Idp::TABLE_COLUMNS);


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


        /* Loop and order by for each sort */
        foreach ($this->sort as $key => $sort) {
            $select->orderBy($sort['column'], $sort['direction']);
        }


        return $select->with($this->getTableWiths());
    }

    /**
     * Array of datas needed for the employee table list
     */
    public function getTableWiths() {
        return [
            'competency' => function($query) {
                $query->select(IdpCompetency::MINIMAL_COLUMNS);
            },            
            'employee' => function($query) {
                $query->select(User::MINIMAL_COLUMNS);
            },
            'employee.supervisor' => function($query) {
                $query->select(User::MINIMAL_COLUMNS);
            },    
            'employee.department' => function($query) {
                $query->select(DepartmentEmployee::MINIMAL_COLUMNS);
            },
            'employee.department.team' => function($query) {
                $query->select(Team::MINIMAL_COLUMNS);
            },                        
            'employee.department.position' => function($query) {
                $query->select(Position::MINIMAL_COLUMNS);
            },
            'approvers' => function($query) {
                $query->select(IdpApprover::MINIMAL_COLUMNS);
            },
            'approvers.approver' => function($query) {
                $query->select(User::MINIMAL_COLUMNS);
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
            'column' => 'employee_id',
            'array' => $employeeIDs
        ]);
    }

    /**
     * Fetches all employee IDs handled by the user's role
     */ 
    protected function getIncludedEmployeeIDs() {

        /* Set necessary data */
        $companies = $this->getCompanyIDs($this->user, 'Adding/Editing of Learning Activities');        
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

        foreach($user->groups as $key => $group) {
            
            if($group->hasRole($role))
                array_push($companies, $group->company_id);
        }

        return $companies;
    }         
}
