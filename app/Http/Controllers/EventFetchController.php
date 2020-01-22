<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Event;
use App\EventTime;

class EventFetchController extends Controller
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

    protected $sort = 'start_date';
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
        $events = $this->fetchQuery($this->where, $this->sort);            


        /* Do the pagination */
        $pagination = $events ? $events->paginate($this->limit) : array_merge($events, ['data' => []]);

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

        if($request->has('search'))
            $this->search = $request->search;

        if($request->has('limit'))
            $this->limit = $request->limit;


        /* Set Where */
        if($request->has('start') && $request->has('end')) {

            $start = $request->input('start');
            $end = $request->input('end');


            array_push($this->where, ['start_date', '>=', $start]);
            array_push($this->where, ['end_date', '<=', $end]);
        }


        /* Set WhereIn */
        if($request->has('search')) {
            array_push($this->whereIn, [
                'column' => 'id',
                'array' => Event::search($request->search)->get()->pluck('id')->toArray()
            ]);
        }


        /* Check whereIn */
        //
    }

    /**
     * Fetch query
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    private function fetchQuery($where, $sort)
    {
        /* Fetch select column including important fields from the ticket object */
        $select = Event::select(Event::TABLE_COLUMNS);


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
                    ->orderBy($sort, 'desc');
    }

    /**
     * Array of datas needed for the employee table list
     */
    public function getTableWiths() {
        return [
        	'times' => function($query) {
                $query->select(EventTime::MINIMAL_COLUMNS);
            },
        ];
    }   
}
