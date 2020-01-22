<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User;
use App\Form;
use App\Ticket;
use App\FormLog;

class RequestLogFetchController extends Controller
{
    protected $user = null;
    protected $form = null;

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
        $logs = $this->fetchQuery($request, $this->where, $this->sort);            


        /* Do the pagination */
        $pagination = $logs ? $logs->paginate($this->limit) : array_merge($logs, ['data' => []]);

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
            $this->form = Form::withTrashed()->find($request->input('request'));

            array_push($this->where, ['form_id', '=', $this->form->id]);
        }


        /* Set WhereIn */
        if($request->has('search')) {
            array_push($this->whereIn, [
                'column' => 'id',
                'array' => FormLog::search($request->input('search'))->get()->pluck('id')->toArray()
            ]);
        }

        if($request->has('archive')) {
            array_push($this->whereIn, [
                'column' => 'id',
                'array' => FormLog::onlyTrashed()->get()->pluck('id')->toArray()
            ]);
        }     


        /* Set WhereNotIn */
        //
    }

    /**
     * Fetch query
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    private function fetchQuery($request, $where, $sort)
    {
        /* Select all resource */
        $select = $request->has('archive') ? FormLog::withTrashed()->select(FormLog::TABLE_COLUMNS) : FormLog::select(FormLog::TABLE_COLUMNS);


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

        return $select->with($this->getTableWiths())->orderBy($sort, 'desc');
    }

    /**
     * Array of datas needed for the user table list
     */
    public function getTableWiths() {
        return [ 
            'form' => function($query) {
                $query->select(Form::MINIMAL_COLUMNS);
            },
            'updater' => function($query) {
                $query->select(User::MINIMAL_COLUMNS);
            },
        ];
    }        
}
