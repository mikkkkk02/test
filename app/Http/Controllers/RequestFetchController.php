<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User;
use App\Company;
use App\Location;
use App\Division;
use App\Department;
use App\DepartmentEmployee;
use App\Position;
use App\Team;
use App\Form;
use App\Ticket;
use App\FormTemplate;
use App\FormTemplateCategory;
use App\FormUpdate;
use App\FormApprover;
use App\Group;

class RequestFetchController extends Controller
{
    protected $user = null;
    protected $companies = [];

    protected $where = [];
    protected $orWhere = [];

    protected $whereInCol = 'forms.id';
    protected $whereIn = [];

    protected $orWhereInCol = 'forms.id';
    protected $orWhereIn = [];

    protected $whereNotInCol = 'forms.id';
    protected $whereNotIn = [];

    protected $sort = 'forms.updated_at';
    protected $sortDir = 'desc';
    protected $search = '';

    protected $limit = 10;


    /**
     * Fetch the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function fetch(Request $request, $export = false)
    {
        /* Get default variable */
        $this->user = \Auth::user();        

        /* Set default filter/search param */
        $this->setParameters($request);


        /* Query */
        $requests = $this->fetchQuery($request, $this->where);
        

        /* Check if this is for export */
        if($export)
            return $requests->get();


        /* Do the pagination */
        $pagination = $requests ? $requests->paginate($this->limit) : array_merge($requests, ['data' => []]);

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
        if($request->has('user') && !$request->has('approval'))
            array_push($this->where, ['forms.employee_id', $request->user]);

        if($request->has('employee') && !$request->has('approval'))
            array_push($this->where, ['forms.employee_id', $request->employee]);

        if($request->has('status'))
            array_push($this->where, ['forms.status', $request->status]);

        if($request->has('template'))
            array_push($this->where, ['forms.form_template_id', $request->template]); 

        if($request->has('form'))
            array_push($this->where, ['forms.form_template_id', $request->form]);         

        if($request->has('start') && $request->has('end')) {

            $start = $request->input('start');
            $end = $request->input('end');


            array_push($this->where, ['forms.created_at', '>=', $start]);
            array_push($this->where, ['forms.created_at', '<=', $end]);
        }

        /* Set orWhere */
        if($request->has('user') && !$request->has('approval'))
            array_push($this->orWhere, ['forms.assignee_id', $request->user]);


        /* Set WhereIn */
        if($request->has('search')) {
            array_push($this->whereIn, [
                'column' => 'forms.id',
                'array' => Form::search($request->search)->get()->pluck('id')->toArray()
            ]);
        }

        if($request->has('archive')) { 
            array_push($this->whereIn, [
                'column' => 'forms.id',
                'array' => Form::onlyTrashed()->get()->pluck('id')->toArray()
            ]);
        }

        if($request->has('myteam')) {
            array_push($this->whereIn, [
                'column' => 'forms.employee_id',
                'array' => User::withTrashed()->findOrFail($request->myteam)->getSubordinateID()
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
                'column' => 'forms.employee_id',
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
                'column' => 'forms.employee_id',
                'array' => $array
            ]);            
        }

        if($request->has('department')) {
            array_push($this->whereIn, [
                'column' => 'forms.employee_id',
                'array' => Department::findOrFail($request->department)->employees()->pluck('employee_id')->toArray()
            ]);
        }

        if($request->has('team')) {
            array_push($this->whereIn, [
                'column' => 'forms.employee_id',
                'array' => Team::findOrFail($request->team)->employees()->pluck('employee_id')->toArray()
            ]);                   
        }      

        if($request->has('approval')) {
            array_push($this->whereIn, [
                'column' => 'forms.id',
                'array' => User::withTrashed()->findOrFail($request->approval)
                                    ->approvals()
                                    ->join('forms', 'form_id', '=', 'forms.id')
                                    ->select(['form_approvers.status AS form_approvers.status', 'form_approvers.enabled AS form_approvers.enabled', 'form_id', 'forms.status AS form.status'])
                                    ->where('form_approvers.status', FormApprover::PENDING)
                                    ->where('forms.status', '!=', Form::CANCELLED)
                                    ->where('forms.status', '!=', Form::DISAPPROVED)
                                    ->where('forms.status', '!=', Form::APPROVED)
                                    ->where('form_approvers.enabled', 1)
                                    ->pluck('form_id')->toArray()
            ]);
        }

        if($request->has('user') && $request->has('ongoing')) {
            $formIDs = [];

            $user = User::withTrashed()->findOrFail($request->user);

            $formIDs = $user->forms()->where('status', Form::PENDING)->pluck('id')->toArray();
            $formIDs = array_merge($formIDs, $user->assignee_forms()->where('status', Form::PENDING)->pluck('id')->toArray());
            $formIDs = array_merge($formIDs, $user->tickets()->whereIn('status', [Ticket::OPEN, Ticket::ONHOLD])->pluck('form_id')->toArray());

            array_push($this->whereIn, [
                'column' => 'forms.id',
                'array' => array_unique($formIDs),
            ]);
        }

        if($request->has('category')) {
            array_push($this->whereIn, [
                'column' => 'forms.form_template_id',
                'array' => FormTemplateCategory::find($request->category)->form_templates()->pluck('id')->toArray()
            ]);
        }

        if($request->has('type')) {
            array_push($this->whereIn, [
                'column' => 'forms.form_template_id',
                'array' => FormTemplate::where('type', $request->type)->pluck('id')->toArray()
            ]);
        }       


        /* Set WhereNotIn */
        if($request->has('xcategory')) {
            array_push($this->whereNotIn, [
                'column' => 'forms.form_template_id',
                'array' => FormTemplateCategory::find($request->xcategory)->form_templates()->pluck('id')->toArray()
            ]);
        }


        /* Set Sort */
        if($request->has('sort')) {
            $sort = $request->input('sort');

            switch ($request->input('sort')) {
                case 'id': 
                    $this->sort = $sort;
                break;               
                case 'created_at':
                    $this->sort = 'forms.created_at';
                break;
                case 'date_closed':
                    $this->sort = 'tickets.date_closed';
                break;
            }

            /* Set order by */
            if($request->has('order'))
                $this->sortDir = $request->input('order');
        }        


        if(!$request->has('approval')) {

            /* Fetch included employees depending on the current users permission */
            $this->getEmployees();
            $this->getFormTypes();
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
        /* Fetch select column including important fields from the ticket object */
        $selectArray = array_merge(Form::TABLE_COLUMNS, ['tickets.created_at as ticket.created_at', 'tickets.status as ticket.status']);
        $select = $request->has('archive') ? Form::withTrashed()->select($selectArray)->leftJoin('tickets', 'forms.id', '=', 'tickets.form_id') :
                                                Form::select($selectArray)->leftJoin('tickets', 'forms.id', '=', 'tickets.form_id');


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
                    // ->orderBy('tickets.status', 'desc')
                    // ->orderBy('forms.status', 'asc')
                    // ->orderBy('tickets.created_at', 'asc')
                    ->orderBy($this->sort, $this->sortDir);
    }

    /**
     * Array of datas needed for the employee table list
     */
    public function getTableWiths() {
        return [
            'employee' => function($query) {
                $query->select(User::MINIMAL_COLUMNS);
            },
            'employee.department' => function($query) {
                $query->select(DepartmentEmployee::MINIMAL_COLUMNS);
            },
            'employee.department.team' => function($query) {
                $query->select(Team::MINIMAL_COLUMNS);
            },                        
            'employee.department.position' => function($query) {
                $query->select(Position::MINIMAL_COLUMNS);
            },                        
            'employee.department.department' => function($query) {
                $query->select(Department::MINIMAL_COLUMNS);
            },
            'employee.department.department.division' => function($query) {
                $query->select(Division::MINIMAL_COLUMNS);
            },
            'employee.department.department.division.company'  => function($query) {
                $query->select(Company::MINIMAL_COLUMNS);
            },                     
            'template' => function($query) {
                $query->select(FormTemplate::MINIMAL_COLUMNS);
            },
            'approvers' => function($query) {
                $query->select(FormApprover::MINIMAL_COLUMNS);
            },
            'ticket' => function($query) {
                $query->select(Ticket::MINIMAL_COLUMNS);
            },
            'ticket.technician' => function($query) {
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


        /* Set orWhereIn variables */
        array_push($this->whereIn, [
            'column' => 'forms.employee_id',
            'array' => $employeeIDs
        ]);
    }

    private function getFormTypes() {
        $groupTypes = $this->user->groups()->pluck('type')->toArray();

        if (count($groupTypes)) {
            $groupTypes = array_unique($groupTypes);
            $typeIds = [];

            if (!in_array(0, $groupTypes)) {

                foreach ($groupTypes as $groupType) {
                    switch ($groupType) {
                        case Group::ADMIN:
                                $formType = FormTemplate::ADMIN;
                            break;

                        case Group::HR:
                                $formType = FormTemplate::HR;
                            break;

                        case Group::OD:
                                $formType = FormTemplate::OD;
                            break;
                    }
                    array_push($typeIds, $formType);
                }

                $formIds = Form::whereHas('template', function ($query) use ($typeIds) {
                    $query->whereIn('type', $typeIds);
                })->pluck('id')->toArray();

                array_push($this->whereIn, [
                    'column' => 'forms.id',
                    'array' => $formIds,
                ]);
            }
        }
    }

    /**
     * Fetches all employee IDs handled by the user's role
     */ 
    protected function getIncludedEmployeeIDs() {

        /* Set necessary data */
        $companies = $this->getCompanyIDs($this->user, 'Creating/Designing/Editing/Removing of Forms');        
        $ids = [];

        foreach($companies as $key => $tmpCompany) {

            /* Check if role is null, meaning all companies */
            if(!$tmpCompany)
                return User::withTrashed()->get()->pluck('id')->toArray();


            /* Fetch company */
            $company = Company::withTrashed()->findOrFail($tmpCompany);


            foreach($company->divisions as $division) {
                foreach($division->departments as $department) {

                    /* Merge the employee IDs */
                    $ids = array_merge($ids, $department->employees()->pluck('employee_id')->toArray());
                }
            }
        }

        /* Include self */
        $ids = array_merge($ids, [$this->user->id]);


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
     * Fetch updates on the resource
     * 
     * @param  int  $id
     * @return \Illuminate\Http\Response 
     */
    public function fetchUpdates($id) {

        $form = Form::withTrashed()->findOrFail($id);
        $updates = $form->updates()
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
        ]);
    }

    /**
     * Display the specified resource's answers.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function fetchAnswer($id)
    {
        $form = Form::withTrashed()->findOrFail($id);


        /* Fetch rendered answers */
        $answers = $form->renderAnswers();

        return response()->json([
            'answers' => $answers,
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
        $form = Form::withTrashed()->findOrFail($id);


        /* Fetch rendered attachments */
        $attachments = $form->attachments;

        return response()->json([
            'attachments' => $attachments,
        ]);
    }    
}
