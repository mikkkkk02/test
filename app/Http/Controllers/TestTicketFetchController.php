<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User;
use App\Company;
use App\Ticket;
use App\TicketTravelOrderDetail;
use App\Form;
use App\FormTemplate;
use App\FormTemplateCategory;

class TestTicketFetchController extends Controller
{
    protected $user = null;
    protected $companies = [];

    protected $where = [];
    protected $orWhere = [];

    protected $whereInCol = 'tickets.id';
    protected $whereIn = [];

    protected $orWhereInCol = 'tickets.id';
    protected $orWhereIn = [];

    protected $whereNotInCol = 'tickets.id';
    protected $whereNotIn = [];

    protected $whereDate = [];

    protected $sort = 'tickets.start_date';
    protected $sortDir = 'desc';
    protected $search = '';


    /**
     * Fetch the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function datatable(Request $request)
    {  
        $start = $request->startdate;
        $end = $request->enddate;
        $status = $request->status;
        /* Get default variable */

        $this->user = \Auth::user();


        $all = Ticket::getAllTicket($start, $end, $status);  



        return response()->json([
            'draw' => 1,
            'recordsTotal' => count($all),
            'recordsFiltered' => count($all),
            'data' => $all
        ]);
       
    }
    public function fetch(Request $request)
    {
        /* Get default variable */
        $this->user = \Auth::user();        

        /* Set default filter/search param */
        $this->setParameters($request);

        /* Query */
        $tickets = $this->fetchQuery($request, $this->where);    
        

        /* Do the pagination */
        $pagination = $tickets ? $tickets->paginate(10) : array_merge($tickets, ['data' => []]);
  
        // $all = Ticket::getAllTicket($params = '');
        
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
        if($request->has('user'))
            array_push($this->where, ['tickets.technician_id', $request->user]);

        if($request->has('status'))
            array_push($this->where, ['tickets.status', $request->status]);

        if($request->has('priority') && $request->priority != 'x')
            array_push($this->where, ['tickets.priority', $request->priority]); 

        if($request->has('state') && $request->state != 'x')
            array_push($this->where, ['tickets.state', $request->state]); 

        if($request->has('start') && $request->has('end')) {

            $start = $request->input('start');
            $end = $request->input('end');


            array_push($this->where, ['tickets.start_date', '>=', $start]);
            array_push($this->where, ['tickets.start_date', '<=', $end]);
        }        


        /* Set WhereIn */
        if($request->has('search')) {
            $array = [];

            /* Search in ID */
            $array = array_merge($array, Form::withTrashed()->where('ticket_id', '!=', null)->where('id', 'LIKE', '%' . $request->input('search') . '%')->get()->pluck('ticket_id')->toArray());
            $array = array_merge($array, Ticket::where('id', 'LIKE', '%' . $request->input('search') . '%')->get()->pluck('id')->toArray());

            array_push($this->whereIn, [
                'column' => 'tickets.id',
                'array' => array_merge($array, Ticket::search($request->input('search'))->get()->pluck('id')->toArray())
            ]);
        }

        if($request->has('all')) {
            array_push($this->whereIn, [
                'column' => 'tickets.id',
                'array' => Ticket::select('id', 'status')->whereIn('status', [Ticket::OPEN, Ticket::ONHOLD, Ticket::CLOSE, Ticket::CANCELLED])->get()->pluck('id')->toArray()
            ]);
    
        }

        if($request->has('ongoing')) {
            array_push($this->whereIn, [
                'column' => 'tickets.id',
                'array' => Ticket::select('id', 'status')->whereIn('status', [Ticket::OPEN, Ticket::ONHOLD])->get()->pluck('id')->toArray()
            ]);            
        }

        if($request->has('category')) {

            $formTempIDs = FormTemplate::withTrashed()->select('id', 'form_template_category_id')->where('form_template_category_id', $request->input('category'))->get()->pluck('id')->toArray();

            array_push($this->whereIn, [
                'column' => 'tickets.id',
                'array' => Form::withTrashed()->select('id', 'form_template_id', 'ticket_id')->whereIn('form_template_id', $formTempIDs)->get()->pluck('ticket_id')->toArray()
            ]);
        }

        if($request->has('form')) {

            $formTempIDs = FormTemplate::withTrashed()->select('id')->where('id', $request->input('form'))->get()->pluck('id')->toArray();

            array_push($this->whereIn, [
                'column' => 'tickets.id',
                'array' => Form::withTrashed()->select('id', 'form_template_id', 'ticket_id')->whereIn('form_template_id', $formTempIDs)->get()->pluck('ticket_id')->toArray()
            ]);
        }        


        /* Set OrWhereIn */
        // 


        if(!$request->has('user')) {
            
            /* Fetch included employees depending on the current users permission */
            $this->getEmployees();

            /* Fetch included tickets */
            $this->getTickets();
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
        /* Select var */
        $select = null;
        $columns = Ticket::JOIN_COLUMNS;


        /* Set Sort */
        if($request->has('sort')) {
            $this->sort = $request->input('sort');

            /* Set order by */
            if($request->has('order'))
                $this->sortDir = $request->input('order');            


            switch($request->input('sort')) {
                /* Columns on tickets table */
                case 'id': 
                case 'form_id':
                case 'priority': case 'status': case 'state':                
                case 'start_date': case 'date_closed': case 'created_at':
                    $this->sort = 'tickets.' . $request->input('sort');

                    /* Select all Tickets */
                    $select = Ticket::select($columns);

                break;
                /* Columns outside of tickets table */
                case 'sla': case 'name':

                    $columns = array_merge($columns, [
                                        'forms.id as forms.id',
                                        'forms.form_template_id as forms.form_template_id',
                                        'form_templates.id as form_templates.id',
                                        'form_templates.sla',
                                        'form_templates.name'
                                    ]
                                );

                    $select = Ticket::select($columns)
                                    ->leftJoin('forms', 'forms.id', '=', 'tickets.form_id')
                                    ->leftJoin('form_templates', 'forms.form_template_id', '=', 'form_templates.id');

                break;
                case 'category':
                    $this->sort = 'form_template_categories.name';                

                    $columns = array_merge($columns, [
                                        'forms.id as forms.id',
                                        'forms.form_template_id as forms.form_template_id',
                                        'form_templates.form_template_category_id as form_templates.form_template_category_id',
                                        'form_template_categories.name as form_template_categories.name',
                                    ]
                                );

                    $select = Ticket::select($columns)
                                    ->leftJoin('forms', 'forms.id', '=', 'tickets.form_id')
                                    ->leftJoin('form_templates', 'forms.form_template_id', '=', 'form_templates.id')
                                    ->leftJoin('form_template_categories', 'form_templates.form_template_category_id', '=', 'form_template_categories.id');                    

                break;
                case 'employee_id': case 'technician_id':
                    $this->sort = 'last_name';

                    $columns = array_merge($columns, [
                                        'users.id as users.id',
                                        'users.last_name as users.last_name'
                                    ]
                                );

                    $select = Ticket::select($columns)
                                    ->leftJoin('users', 'users.id', '=', 'tickets.' . $request->input('sort'));

                break;
            }
        } else {
            /* Select all Tickets */
            $select = Ticket::select($columns);
        }


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
            'form' => function($query) {
                $query->select(Form::MINIMAL_COLUMNS);
            },
            'form.template' => function($query) {
                $query->select(FormTemplate::MINIMAL_COLUMNS);
            }, 
            'form.template.category' => function($query) {
                $query->select(FormTemplateCategory::MINIMAL_COLUMNS);
            },                       
            'owner' => function($query) {
                $query->select(User::MINIMAL_COLUMNS);
            },
            'technician' => function($query) {
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
            'column' => 'tickets.id',
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
            'column' => 'tickets.id',
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
                if($group->type == 0) {
                    return Ticket::select(Ticket::MINIMAL_COLUMNS)
                                    ->get()
                                    ->pluck('id')
                                    ->toArray();
                }


                /* Fetch tickets */
                $ids = Ticket::join('forms', 'forms.id', '=', 'tickets.form_id')
                                ->join('form_templates', 'form_templates.id', '=', 'forms.form_template_id')
                                ->select(['tickets.id AS id', 'tickets.form_id AS tickets.form_id', 'forms.id AS forms.id', 'forms.form_template_id AS forms.form_template_id', 'form_templates.id AS form_templates.id', 'form_templates.type AS form_templates.type'])
                                ->where('form_templates.type', ($group->type - 1))
                                ->get()
                                ->pluck('id')
                                ->toArray();

                                // http_response_code(500); dd($ids);


                array_push($tickets, $ids);
            }
        }

        /* Fetch ticket that is assigned to the current user */
        $tickets = array_merge($tickets, $this->user->getTicketApproval()->pluck('id')->toArray());

        $tickets = $this->flatten($tickets);

        return array_unique($tickets);
    }

    private function flatten(array $array) {
        $return = array();
        array_walk_recursive($array, function($a) use (&$return) { $return[] = $a; });
        return $return;
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

    /**
     * Fetch updates on the resource
     * 
     * @param  int  $id
     * @return \Illuminate\Http\Response 
     */
    public function fetchUpdates($id) {

        $ticket = Ticket::findOrFail($id);
        $type = $ticket->form->template->name;
        $updates = $ticket->updates()
                        ->with([
                            'employee' => function($query) {
                                $query->select(User::MINIMAL_COLUMNS);
                            }
                        ])
                        ->orderBy('created_at', 'desc');


        /* Do the pagination */
        $pagination = $updates ? $updates->paginate(5) : array_merge($updates, ['data' => []]);

        return response()->json([
            'response' => 1,
            'lists' => $pagination,
            'form_type' => $type
        ]);
    }

    /**
     * Display the specified resource's attachments.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function fetchAttachments($id)
    {
        $ticket = Ticket::findOrFail($id);


        /* Fetch rendered attachments */
        $attachments = $ticket->attachments;

        return response()->json([
            'attachments' => $attachments,
        ]);
    }

    public function fetchTravelOrderDetails(Ticket $ticket)
    {
        $ids = $ticket->travel_order_details()->pluck('id');

        $travelOrderDetails = TicketTravelOrderDetail::whereIn('id', $ids)->get();

        return response()->json([
            'lists' => $travelOrderDetails,
        ]);
    }
}