<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User;
use App\Company;
use App\Position;
use App\Department;

class PositionFetchController extends Controller
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
        $positions = $this->fetchQuery($this->where, $this->sort);            


        /* Do the pagination */
        $pagination = $positions ? $positions->paginate(10) : array_merge($positions, ['data' => []]);

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
                'array' => Position::search($request->search)->get()->pluck('id')->toArray()
            ]);
        }
        
        if($request->has('department')) {
            array_push($this->whereIn, [
                'column' => 'id',
                'array' => Department::findOrFail($request->department)->positions()->pluck('id')->toArray()
            ]);
        }


        /* Set WhereNotIn */
        if($request->has('xdepartment')) {
            array_push($this->whereNotIn, [
                'column' => 'id',
                'array' => Department::findOrFail($request->xdepartment)->positions()->pluck('id')->toArray()
            ]);            
        }


        /* Fetch included positions depending on the current users permission */
        $this->getPositions();       
    }

    /**
     * Fetch query
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    private function fetchQuery($where, $sort)
    {
        /* Select all Users */
        $select = Position::select(Position::TABLE_COLUMNS);


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

        return $select->with($this->getTableWiths())->orderBy($sort, 'asc');
    }

    /**
     * Array of datas needed for the positions table list
     */
    public function getTableWiths() {
        return [
            'department' => function($query) {
                $query->select(Department::MINIMAL_COLUMNS);
            },
        ];
    }


    /**
     * Fetches all positions that is handled by the current user
     */    
    public function getPositions() {

        /* Fetch all included positions IDs */
        $positionIDs = $this->getIncludedPositionIDs();


        /* Set orWhereIn variables */
        array_push($this->whereIn, [
            'column' => 'id',
            'array' => $positionIDs
        ]);
    }

    /**
     * Fetches all positions IDs handled by the user's role
     */ 
    protected function getIncludedPositionIDs() {

        /* Set necessary data */
        $companies = $this->getCompanyIDs($this->user, 'Adding/Editing of Positions');        
        $ids = [];

        foreach($companies as $key => $tmpCompany) {

            /* Check if role is null, meaning all companies */
            if(!$tmpCompany)
                return Position::get()->pluck('id')->toArray();


            /* Fetch company */
            $company = Company::withTrashed()->findOrFail($tmpCompany);


            foreach($company->divisions as $division) {
                foreach($division->departments as $department) {

                    /* Merge the positions IDs */
                    $ids = array_merge($ids, $department->positions()->pluck('id')->toArray());
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
