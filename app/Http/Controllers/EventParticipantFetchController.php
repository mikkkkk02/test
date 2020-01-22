<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User;
use App\DepartmentEmployee;
use App\Department;
use App\Division;
use App\Company;
use App\Team;
use App\Position;
use App\Form;
use App\Event;
use App\EventParticipant;

class EventParticipantFetchController extends Controller
{
    protected $where = [];
    protected $whereIn = [];
    protected $whereInCol = 'id';
    protected $whereNotIn = [];
    protected $sort = 'approved_at';
    protected $search = '';


    /**
     * Fetch the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function fetch(Request $request, $export = false)
    {
        /* Get all requests */
        //

        $this->setParameters($request);


        /* Check if search parameter is active */
        if($this->search) {

            /* Query according to the search parameter and get only the collection of ID */ 
            array_push($this->whereIn, [
                'column' => 'id',
                'array' => EventParticipant::search($this->search)->get()->pluck('id')->toArray()
            ]);                                     

            /* Include it to the where array */
            if($this->whereIn) {

                $events = $this->fetchQuery($this->where, $this->whereIn, $this->whereNotIn, $this->sort);

            } else {
                $events = [];
            }

        } else {
            $events = $this->fetchQuery($this->where, $this->whereIn, $this->whereNotIn, $this->sort);            
        }


        /* Check if this is for export */
        if($export)
            return $events->get();


        /* Do the pagination */
        $pagination = $events ? $events->paginate(10) : array_merge($events, ['data' => []]);

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


        /* Set Where */
        if($request->has('user'))
            array_push($this->where, ['participant_id', $request->user]);

        if($request->has('employee'))
            array_push($this->where, ['participant_id', $request->employee]);        

        if($request->has('status'))
            array_push($this->where, ['status', $request->status]);

        if($request->has('attendance'))
            array_push($this->where, ['hasAttended', $request->attendance]);

        if($request->has('approver')) {
            array_push($this->where, ['status', EventParticipant::PENDING]);
            array_push($this->where, ['approver_id', $request->approver]);
        }

        if($request->has('event'))
            array_push($this->where, ['event_id', $request->event]);

        if($request->has('attendance'))
            array_push($this->where, ['hasAttended', $request->attendance]);

        if($request->has('pending') && $request->has('event'))
            array_push($this->where, ['status', '!=', EventParticipant::APPROVED]);

        if($request->has('inqueue') && $request->has('event'))
            array_push($this->where, ['status', EventParticipant::APPROVED]);

        if($request->has('participant') && $request->has('event'))
            array_push($this->where, ['status', EventParticipant::APPROVED]);

        if($request->has('report') && $request->has('start') && $request->has('end')) {

            $start = $request->input('start');
            $end = $request->input('end');


            array_push($this->where, ['created_at', '>=', $start]);
            array_push($this->where, ['created_at', '<=', $end]);
        }        


        /* Set WhereIn */
        if($request->has('participant') && $request->has('event')) {
            array_push($this->whereIn, [
                'column' => 'id',
                'array' => Event::find($request->event)->getAttendingParticipants()->pluck('id')->toArray()
            ]);         
        }

        if($request->has('myteam')) {
            array_push($this->whereIn, [
                'column' => 'participant_id',
                'array' => User::withTrashed()->find($request->myteam)->getSubordinateID()
            ]);             
        }

        if($request->has('company')) { 
            $company = Company::withTrashed()->findOrFail($request->company);
            $array = [];

            foreach ($company->divisions as $division) {
                foreach ($division->departments as $department) {

                    /* Merge the employee IDs */
                    $array = array_merge($array, $department->employees()->pluck('employee_id')->toArray());
                }
            }

            array_push($this->whereIn, [
                'column' => 'participant_id',
                'array' => $array
            ]);
        }

        if($request->has('division')) {
            $division = Division::withTrashed()->findOrFail($request->division);
            $array = [];

            foreach ($division->departments as $department) {

                /* Merge the employee IDs */
                $array = array_merge($array, $department->employees()->pluck('employee_id')->toArray());
            }

            array_push($this->whereIn, [
                'column' => 'participant_id',
                'array' => $array
            ]);            
        }

        if($request->has('department')) {
            array_push($this->whereIn, [
                'column' => 'participant_id',
                'array' => Department::findOrFail($request->department)->employees()->pluck('employee_id')->toArray()
            ]);
        }

        if($request->has('team')) {
            array_push($this->whereIn, [
                'column' => 'participant_id',
                'array' => Team::findOrFail($request->team)->employees()->pluck('employee_id')->toArray()
            ]);                   
        }         

        if($request->has('inqueue') && $request->has('event'))
            $this->whereNotIn = Event::find($request->event)->getAttendingParticipants()->pluck('id')->toArray();        


        /* Check whereIn */
        if(($request->has('participant') && $request->has('event')) || $request->has('team')) {

            /* Set 0 value if collection is empty to still trigger the whereIn method */
            $this->whereIn = $this->whereIn ? $this->whereIn : [[]];
        }
    }

    /**
     * Fetch query
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    private function fetchQuery($where, $whereIn, $whereNotIn, $sort)
    {
        /* Check if there is a whereIn clause */
        if($whereIn) {

            $select = EventParticipant::select(EventParticipant::TABLE_COLUMNS);


            /* Loop and filter for each whereIn */
            foreach ($this->whereIn as $key => $whereIn) {
                $select->whereIn($whereIn['column'], $whereIn['array']);
            }

            /* Apply all remaining conditions */
            return $select->whereNotIn('id', $whereNotIn)
                        ->where($where)
                        ->with($this->getTableWiths())
                        ->orderBy($sort, 'asc');

        } else {

            return EventParticipant::select(EventParticipant::TABLE_COLUMNS)
                        ->whereNotIn('id', $whereNotIn)
                        ->where($where)
                        ->with($this->getTableWiths())
                        ->orderBy($sort, 'asc');

        }
    }

    /**
     * Array of datas needed for the employee table list
     */
    public function getTableWiths() {
        return [
            'event' => function($query) {
                $query->select(Event::MINIMAL_COLUMNS);
            },
            'participant' => function($query) {
                $query->select(User::MINIMAL_COLUMNS);
            },
            'participant.department' => function($query) {
                $query->select(DepartmentEmployee::MINIMAL_COLUMNS);
            },
            'participant.department.department.division' => function($query) {
                $query->select(Division::MINIMAL_COLUMNS);
            },
            'participant.department.department.division.company' => function($query) {
                $query->select(Company::MINIMAL_COLUMNS);
            },                        
            'participant.department.department' => function($query) {
                $query->select(Department::MINIMAL_COLUMNS);
            },            
            'participant.department.team' => function($query) {
                $query->select(Team::MINIMAL_COLUMNS);
            },                        
            'participant.department.position' => function($query) {
                $query->select(Position::MINIMAL_COLUMNS);
            },            
            'form' => function($query) {
                $query->select(Form::MINIMAL_COLUMNS);
            },                       
            'approver' => function($query) {
                $query->select(User::MINIMAL_COLUMNS);
            },
        ];
    }    
}
