<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User;
use App\Division;
use App\Company;

class DivisionFetchController extends Controller
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
        $divisions = $this->fetchQuery($request, $this->where);            


        /* Do the pagination */
        $pagination = $divisions ? $divisions->paginate(10) : array_merge($divisions, ['data' => []]);

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
                'array' => Division::search($request->search)->get()->pluck('id')->toArray()
            ]);
        }

        if($request->has('company')) {
            array_push($this->whereIn, [
                'column' => 'id',
                'array' => Company::withTrashed()->findOrFail($request->company)->divisions()->pluck('id')->toArray()
            ]); 
        }


        /* Set WhereNotIn */
        if($request->has('xcompany')) {
            array_push($this->whereNotIn, [
                'column' => 'id',
                'array' => Company::withTrashed()->findOrFail($request->xcompany)->divisions()->pluck('id')->toArray()
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


        /* Fetch included divisions depending on the current users permission */
        $this->getDivisions();
    }

    /**
     * Fetch query
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    private function fetchQuery($request, $where)
    {
        /* Select all Divisions */
        $select = $request->has('archive') ? Division::withTrashed()->select(Division::TABLE_COLUMNS) : Division::select(Division::TABLE_COLUMNS);


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
            'company' => function($query) {
                $query->select(Company::MINIMAL_COLUMNS);
            },
        ];
    }   

    /**
     * Fetches all division that is handled by the current user
     */    
    public function getDivisions() {

        /* Fetch all included employee IDs */
        $divisionIDs = $this->getIncludedDivisionIDs();


        /* Set orWhereIn variables */
        array_push($this->whereIn, [
            'column' => 'id',
            'array' => $divisionIDs
        ]);
    }

    /**
     * Fetches all division IDs handled by the user's role
     */ 
    protected function getIncludedDivisionIDs() {

        /* Set necessary data */
        $companies = $this->getCompanyIDs($this->user, 'Adding/Editing of Group');        
        $ids = [];

        foreach($companies as $key => $tmpCompany) {

            /* Check if role is null, meaning all companies */
            if(!$tmpCompany)
                return Division::withTrashed()->get()->pluck('id')->toArray();


            /* Fetch company */
            $company = Company::withTrashed()->findOrFail($tmpCompany);

            /* Merge the division IDs */
            $ids = array_merge($ids, $company->divisions()->pluck('id')->toArray());
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
     * Fetch the available departments for the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function fetchDepartments(Request $request, $id)
    {
    	$division = Division::findOrFail($id);


    	$departments = $division->departments()->select(['id', 'name'])->get();

        return response()->json([
            'lists' => $departments,
        ]);
    }
}
