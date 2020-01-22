<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User;
use App\Group;
use App\Company;

class GroupFetchController extends Controller
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
        $groups = $this->fetchQuery($request, $this->where);            


        /* Do the pagination */
        $pagination = $groups ? $groups->paginate(10) : array_merge($groups, ['data' => []]);

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


        /* Set WhereIn */
        if($request->has('search')) {
            array_push($this->whereIn, [
                'column' => 'id',
                'array' => Group::search($request->search)->get()->pluck('id')->toArray()
            ]);
        }

        if($request->has('archive')) {
            array_push($this->whereIn, [
                'column' => 'id',
                'array' => Group::onlyTrashed()->get()->pluck('id')->toArray()
            ]);
        }


        /* Check whereNotIn */
        //     


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
        $select = $request->has('archive') ? Group::withTrashed()->select(Group::TABLE_COLUMNS) : Group::select(Group::TABLE_COLUMNS);


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
}
