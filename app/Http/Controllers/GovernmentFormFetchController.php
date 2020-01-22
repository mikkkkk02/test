<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User;
use App\GovernmentForm;

class GovernmentFormFetchController extends Controller
{
    protected $user = null;

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
        $teams = $this->fetchQuery($request, $this->where);


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
                'array' => GovernmentForm::search($request->search)->get()->pluck('id')->toArray()
            ]);
        }

       if($request->has('archive')) {
            array_push($this->whereIn, [
                'column' => 'id',
                'array' => GovernmentForm::onlyTrashed()->get()->pluck('id')->toArray()
            ]);
        }        


        /* Set WhereNotIn */
        //
    

        /* Set Sort */
        if($request->has('sort')) {
            $sort = $request->input('sort');

            switch ($request->input('sort')) {
                case 'name': case 'updated_at':
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
        /* Select all Government forms */
        $select = $request->has('archive') ? GovernmentForm::withTrashed()->select(GovernmentForm::TABLE_COLUMNS) : GovernmentForm::select(GovernmentForm::TABLE_COLUMNS);


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
            'updater' => function($query) {
                $query->select(User::MINIMAL_COLUMNS);
            },            
        ];
    }      
}
