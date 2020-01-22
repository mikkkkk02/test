<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User;
use App\Ticket;
use App\TicketUpdate;
use App\TempTicketUpdate;

class TempTicketUpdateFetchController extends Controller
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
        $tmpTicketUpdates = $this->fetchQuery($request, $this->where);            


        /* Do the pagination */
        $pagination = $tmpTicketUpdates ? $tmpTicketUpdates->paginate(10) : array_merge($tmpTicketUpdates, ['data' => []]);

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

        if($request->has('status'))
            array_push($this->where, ['status', $request->input('status')]);

        if($request->has('done'))
            array_push($this->where, ['status', '!=', $request->input('done')]);


        /* Set WhereIn */
        if($request->has('search')) {
            array_push($this->whereIn, [
                'column' => 'id',
                'array' => TempTicketUpdate::search($request->search)->get()->pluck('id')->toArray()
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

        /* Fetch included employees depending on the current users permission */
        $this->getEmployees();

        /* Fetch included tickets */
        $this->getTickets();        
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
        $select = $request->has('archive') ? TempTicketUpdate::withTrashed()->select(TempTicketUpdate::TABLE_COLUMNS) : TempTicketUpdate::select(TempTicketUpdate::TABLE_COLUMNS);


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
            'ticket' => function($query) {
                $query->select(Ticket::MINIMAL_COLUMNS);
            },
            'employee' => function($query) {
                $query->select(User::MINIMAL_COLUMNS);
            },                    
            'approver' => function($query) {
                $query->select(User::MINIMAL_COLUMNS);
            },
		];
    }

/**
     * Fetches all employees that is handled by the current user
     */    
    public function getEmployees() {

        /* Fetch all included employee IDs */
        $employeeIDs = $this->getIncludedEmployeeIDs();


        /* Set whereIn variables */
        array_push($this->whereIn, [
            'column' => 'ticket_id',
            'array' => $employeeIDs
        ]);

        // http_response_code(500);
        // dd($employeeIDs);
    }

    /**
     * Fetches all tickets that is handled by the current user
     */    
    public function getTickets() {

        /* Fetch all included ticket IDs */
        $ticketIDs = $this->getIncludedTicketIDs();


        /* Include assigned tickets */        
        if($this->user->getTicketApproval()->count())
            $ticketIDs = array_merge($ticketIDs, $this->user->getTicketApproval()->pluck('id')->toArray());


        /* Set whereIn variables */
        array_push($this->whereIn, [
            'column' => 'ticket_id',
            'array' => $ticketIDs
        ]); 
    }    

    /**
     * Fetches all employee IDs handled by the user's role
     */ 
    protected function getIncludedEmployeeIDs() {

        /* Set necessary data */
        $companies = $this->getCompanyIDs($this->user, ['Editing/Removing of Tickets', 'Updating of Ticket Status', 'Generating of Ticketing Reports']);
        $ids = [];

        foreach($companies as $key => $tmpCompany) {

            /* Check if role is null, meaning all companies */
            if(!$tmpCompany)
                return Ticket::get()->pluck('id')->toArray();


            /* Fetch company */
            $company = Company::withTrashed()->findOrFail($tmpCompany);


            foreach($company->divisions as $division) {
                foreach($division->departments as $department) {
                    foreach($department->employees as $employee) {

                        /* Merge the ticket IDs */
                        if($employee->employee)
                            $ids = array_merge($ids, $employee->employee->tickets()->pluck('id')->toArray());
                    }
                }
            }
        }

        /* Fetch ticket that is assigned to the current user */
        $ids = array_merge($ids, $this->user->getTicketApproval()->pluck('id')->toArray());


        /* Include self */
        $ids = array_merge($ids, $this->user->tickets()->pluck('id')->toArray());
        
        return $ids;
    }

    /**
     * Fetches all ticket IDs handled by the user's role
     */ 
    protected function getIncludedTicketIDs() {

        /* Set necessary data */  
        $tickets = [];


        foreach ($this->user->groups as $key => $group) {
            
            if($group->hasRole('Updating of Ticket Status') || 
                $group->hasRole('Editing/Removing of Tickets') ||
                $group->hasRole('Generating of Ticketing Reports')) {

                /* If one group has access to all return immediately */
                if($group->type == 0)
                    return Ticket::select(Ticket::MINIMAL_COLUMNS)
                                    ->get()
                                    ->pluck('id')
                                    ->toArray();


                /* Fetch tickets */
                $ids = Ticket::join('forms', 'forms.id', '=', 'tickets.form_id')
                                ->join('form_templates', 'form_templates.id', '=', 'forms.form_template_id')
                                ->select(['tickets.id AS id', 'tickets.form_id AS tickets.form_id', 'forms.id AS forms.id', 'forms.form_template_id AS forms.form_template_id', 'form_templates.id AS form_templates.id', 'form_templates.type AS form_templates.type'])
                                ->where('form_templates.type', ($group->type - 1))
                                ->get()
                                ->pluck('id')
                                ->toArray();

                                // http_response_code(500); dd($ids);


                $tickets = array_merge($tickets, $ids);
            }
        }

        /* Fetch ticket that is assigned to the current user */
        $tickets = array_merge($tickets, $this->user->getTicketApproval()->pluck('id')->toArray());


        return array_unique($tickets);
    }    

    /**
     * Fetch all company IDs the current user has permission to 
     *
     * @return array
     */
    protected function getCompanyIDs($user, $roles) {
        $companies = [];

        foreach ($user->groups as $key => $group) {
            foreach ($roles as $key => $role) {
                if($group->hasRole($role))
                    array_push($companies, $group->company_id);
            }
        }

        return $companies;
    }         
}
