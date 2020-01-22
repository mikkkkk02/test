<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User;
use App\Form;
use App\FormHistory;

class RequestHistoryFetchController extends Controller
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
    protected $sortDir = 'desc';
    protected $search = '';

    protected $limit = 10;


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
        $requests = $this->fetchQuery($request, $this->where);
        

        /* Check if this is for export */
        if($export)
            return $requests->get();


        /* Do the pagination */
        $pagination = $requests ? $requests->paginate($this->limit) : array_merge($requests, ['data' => []]);

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
        if($request->has('request')) {
            array_push($this->where, ['form_id', $request->input('request')]);
        } else {
        	return response()->json([
        			'reponse' => 0
        		]);
        }


        /* Set orWhere */
      	//


        /* Set WhereIn */
		//      


        /* Set WhereNotIn */
        //


        /* Set Sort */
        if($request->has('sort')) {
            $sort = $request->input('sort');

            switch ($request->input('sort')) {
                case 'id': 
                    $this->sort = $sort;
                break;               
                case 'created_at':
                    $this->sort = 'created_at';
                break;
            }

            /* Set order by */
            if($request->has('order'))
                $this->sortDir = $request->input('order');
        }        


        /* Fetch included employees depending on the current users permission */
        // $this->getEmployees();
    }

    /**
     * Fetch query
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    private function fetchQuery($request, $where)
    {
        /* Fetch select column including important fields from the ticket object */
        $select = $request->has('archive') ? FormHistory::withTrashed()->select(FormHistory::TABLE_COLUMNS) : 
        									FormHistory::select(FormHistory::TABLE_COLUMNS);


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
			//            
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
            'column' => 'form_id',
            'array' => $employeeIDs
        ]);
    }

    /**
     * Fetches all employee IDs handled by the user's role
     */ 
    protected function getIncludedEmployeeIDs() {

        /* Set necessary data */
        $companies = $this->getCompanyIDs($this->user, 'Creating/Designing/Editing/Removing of Forms');        
        $ids = [];

        foreach($companies as $key => $tmpCompany) {

            /* Check if role is null, meaning all companies */
            if(!$tmpCompany)
                return FormHistory::get()->pluck('id')->toArray();


            /* Fetch company */
            $company = Company::withTrashed()->findOrFail($tmpCompany);


            foreach($company->divisions as $division) {
                foreach($division->departments as $department) {
                    foreach($department->employees as $employee) {

                        /* Merge the ticket IDs */
                        if($employee->employee)
                            $ids = array_merge($ids, $employee->employee->forms()->pluck('id')->toArray());
                    }
                }
            }
        }

        /* Include self */
        $ids = array_merge($ids, $this->user->approvals()->pluck('form_id')->toArray());

        /* Include self */
        $ids = array_merge($ids, $this->user->forms()->pluck('id')->toArray());


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
