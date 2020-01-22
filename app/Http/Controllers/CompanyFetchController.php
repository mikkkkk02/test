<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User;
use App\Company;
use App\Division;

class CompanyFetchController extends Controller
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

    protected $sort = 'name';
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
        $companies = $this->fetchQuery($request, $this->where);


        /* Do the pagination */
        $pagination = $companies ? $companies->paginate(10) : array_merge($companies, ['data' => []]);

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
        if($request->has('archive')) {
            array_push($this->whereIn, [
                'column' => 'id',
                'array' => Company::onlyTrashed()->get()->pluck('id')->toArray()
            ]);
        }


        /* Check whereIn */
        if($request->has('search')) {
            array_push($this->whereIn, [
                'column' => 'id',
                'array' => Company::search($request->search)->get()->pluck('id')->toArray()
            ]);
        }


        /* Set Sort */
        if($request->has('sort')) {
            $sort = $request->input('sort');

            switch ($request->input('sort')) {
                case 'name': case 'abbreviation': 
                    $this->sort = $sort;
                break;
            }

            /* Set order by */
            if($request->has('order'))
                $this->sortDir = $request->input('order');
        }          


        /* Fetch included companies depending on the current users permission */
        $this->getCompanies();
    }

    /**
     * Fetch query
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    private function fetchQuery($request, $where)
    {
        /* Select all Companies */
        $select = $request->has('archive') ? Company::withTrashed()->select(Company::TABLE_COLUMNS) : Company::select(Company::TABLE_COLUMNS);


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
     * Array of datas needed for the company table list
     */
    public function getTableWiths() {
        return [
            //
        ];
    }

    /**
     * Fetches all companies that is handled by the current user
     */    
    public function getCompanies() {

        /* Fetch all included employee IDs */
        $companyIDs = $this->getIncludedCompanyIDs();


        /* Set orWhereIn variables */
        array_push($this->whereIn, [
            'column' => 'id',
            'array' => $companyIDs
        ]);
    }

    /**
     * Fetches all company IDs handled by the user's role
     */ 
    protected function getIncludedCompanyIDs() {

        /* Set necessary data */
        $companies = $this->getCompanyIDs($this->user, 'Adding/Editing of Employee Profile');        
        $ids = [];

        foreach($companies as $key => $tmpCompany) {

            /* Check if role is null, meaning all companies */
            if(!$tmpCompany)
                return Company::withTrashed()->get()->pluck('id')->toArray();


            /* Merge the company IDs */
            $ids = array_merge($ids, [$tmpCompany]);
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
     * Fetch the available divisions for the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function fetchDivisions(Request $request, $id)
    {
    	$company = Company::findOrFail($id);


    	$divisions = $company->divisions()->select(Division::MINIMAL_COLUMNS)->get();

        return response()->json([
            'lists' => $divisions,
        ]);    	
    }   
}
