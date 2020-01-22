<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\TempIdp;
use App\Idp;
use App\IdpCompetency;
use App\User;
use App\Company;
use App\Department;
use App\DepartmentEmployee;
use App\Team;
use App\Position;

class TempIDPFetchController extends Controller
{
    protected $user = null;
    protected $companies = [];

    protected $where = [];
    protected $orWhere = [];

    protected $whereIn = [];
    protected $whereInCol = 'id';    

    protected $orWhereIn = [];
    protected $orWhereInCol = 'id';

    protected $whereNotInCol = 'id';
    protected $whereNotIn = [];

    protected $sort = 'created_at';
    protected $search = '';


    /**
     * Fetch the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function fetch(Request $request)
    {
        /* Fetch user */
        $this->user = \Auth::user();

        /* Set default filter/search param */
        $this->setParameters($request);


        /* Query */
        $idps = $this->fetchQuery($this->where, $this->sort);


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


        /* Set whereIn */
        if($request->has('search')) {
            array_push($this->whereIn, [
                'column' => 'forms.id',
                'array' => TempIdp::search($request->search)->get()->pluck('id')->toArray()
            ]);
        }        


        /* Fetch included employees depending on the current users permission */
        $this->getEmployees();
        /* Fetch for approval IDPs */
        $this->getForApproval();
    }

    /**
     * Fetch query
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    private function fetchQuery($where, $sort)
    {
        /* Select all TempIDP */
        $select = TempIdp::select(TempIdp::TABLE_COLUMNS);


        /* Check if there is a whereIn clause */
        foreach ($this->whereIn as $key => $whereIn) {
            $select->whereIn($whereIn['column'], $whereIn['array']);
        }

        foreach ($this->orWhereIn as $key => $orWhereIn) {
            $select->orWhereIn($orWhereIn['column'], $orWhereIn['array']);
        }

        if($this->whereNotIn)
            $select->whereNotIn($this->whereNotInCol, $this->whereNotIn);


        /* Check if there is a where clause */
        if($where)
            $select->where($where);

        if($this->orWhere)
            $select->orWhere($this->orWhere);

        return $select->with($this->getTableWiths())->orderBy($sort, 'asc');
    }

    /**
     * Array of datas needed for the employee table list
     */
    public function getTableWiths() {
        return [
            'competency' => function($query) {
                $query->select(IdpCompetency::MINIMAL_COLUMNS);
            },
            'approver' => function($query) {
                $query->select(User::MINIMAL_COLUMNS);
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

                    /* Merge the team IDs */
                    $ids = array_merge($ids, $department->employees()->pluck('employee_id')->toArray());
                }
            }
        }

        /* Include self */
        $ids = array_merge($ids, [$this->user->id]);


        return $ids;
    }


    /**
     * Fetches all for Approval IDPs of the current user
     */    
    public function getForApproval() {

        /* Set orWhereIn variables */
        array_push($this->orWhereIn, [
            'column' => 'id',
            'array' => $this->user->idp_approvals()->pluck('id')->toArray()
        ]);
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
