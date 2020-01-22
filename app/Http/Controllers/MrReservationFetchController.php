<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\MrReservation;
use App\Location;
use App\Room;

class MrReservationFetchController extends Controller
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

    protected $sort = 'id';
    protected $sortDir = 'desc';
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
        $lists = $this->fetchQuery($request, $this->where);


        /* Do the pagination */
        $pagination = $lists ? $lists->paginate(10) : array_merge($lists, ['data' => []]);

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
                'array' => MrReservation::search($request->search)->get()->pluck('id')->toArray()
            ]);
        }

        if($request->has('archive')) {
            array_push($this->whereIn, [
                'column' => 'id',
                'array' => MrReservation::onlyTrashed()->get()->pluck('id')->toArray()
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
    }

    /**
     * Fetch query
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    private function fetchQuery($request, $where)
    {
        /* Select all Locations */
        $select = $request->has('archive') ? MrReservation::withTrashed()->select(MrReservation::TABLE_COLUMNS) : MrReservation::select(MrReservation::TABLE_COLUMNS);


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
            'location' => function($query) {
                $query->select(Location::MINIMAL_COLUMNS);
            },
            'room' => function($query) {
                $query->select(Room::MINIMAL_COLUMNS);
            },
        ];
    }
}
