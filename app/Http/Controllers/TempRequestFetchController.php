<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User;
use App\Company;
use App\Form;
use App\TempForm;
use App\Ticket;
use App\FormTemplate;
use App\FormTemplateCategory;
use App\FormUpdate;
use App\FormApprover;
use App\TempFormApprover;

class TempRequestFetchController extends Controller
{
    protected $user = null;
    protected $companies = [];

    protected $where = [];
    protected $orWhere = [];

    protected $whereInCol = 'temp_forms.id';
    protected $whereIn = [];

    protected $orWhereInCol = 'temp_forms.id';
    protected $orWhereIn = [];

    protected $whereNotInCol = 'temp_forms.id';
    protected $whereNotIn = [];

    protected $sort = 'temp_forms.created_at';
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
        if($request->has('status'))
            array_push($this->where, ['temp_forms.status', $request->status]);

        if($request->has('done'))
            array_push($this->where, ['temp_forms.status', '!=', TempForm::PENDING]);        

        if($request->has('template'))
            array_push($this->where, ['temp_forms.form_template_id', $request->template]); 

        if($request->has('form'))
            array_push($this->where, ['temp_forms.form_template_id', $request->form]);

        if($request->has('start') && $request->has('end')) {

            $start = $request->input('start');
            $end = $request->input('end');


            array_push($this->where, ['temp_forms.created_at', '>=', $start]);
            array_push($this->where, ['temp_forms.created_at', '<=', $end]);
        }


        /* Set orWhere */
        //


        /* Set WhereIn */
        if($request->has('search')) {
            array_push($this->whereIn, [
                'column' => 'temp_forms.id',
                'array' => TempForm::search($request->search)->get()->pluck('id')->toArray()
            ]);
        }

        if($request->has('archive')) { 
            array_push($this->whereIn, [
                'column' => 'temp_forms.id',
                'array' => TempForm::onlyTrashed()->get()->pluck('id')->toArray()
            ]);
        }

        if($request->has('approval')) {
            array_push($this->whereIn, [
                'column' => 'temp_forms.id',
                'array' => User::withTrashed()->findOrFail($request->approval)
                                    ->update_approvals()
                                    ->join('temp_forms', 'temp_form_id', '=', 'temp_forms.id')
                                    ->select(['temp_form_approvers.status AS temp_form_approvers.status', 'temp_form_approvers.enabled AS temp_form_approvers.enabled', 'temp_form_id', 'temp_forms.status AS temp_forms.status'])
                                    ->where('temp_form_approvers.status', FormApprover::PENDING)
                                    ->where('temp_form_approvers.enabled', 1)
                                    ->pluck('temp_form_id')->toArray()
            ]);

            // dd(User::withTrashed()->findOrFail($request->approval)
            //                         ->update_approvals()
            //                         ->join('temp_forms', 'temp_form_id', '=', 'temp_forms.id')
            //                         ->select(['temp_form_approvers.status AS temp_form_approvers.status', 'temp_form_approvers.enabled AS temp_form_approvers.enabled', 'temp_form_id', 'temp_forms.status AS temp_forms.status'])
            //                         ->where('temp_form_approvers.status', FormApprover::PENDING)
            //                         ->get());
        }


        /* Set WhereNotIn */
        //


        /* Set Sort */
        if($request->has('sort')) {
            $sort = $request->input('sort');

            switch ($request->input('sort')) {
                case 'id': 
                    $this->sort = $sort;
                break;               
                case 'created_at':
                    $this->sort = 'temp_forms.created_at';
                break;
            }

            /* Set order by */
            if($request->has('order'))
                $this->sortDir = $request->input('order');
        }


        if(!$request->has('approval')) {

            /* Fetch included employees depending on the current users permission */
            $this->getEmployees();
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
        $select = $request->has('archive') ? TempForm::withTrashed()->select(TempForm::TABLE_COLUMNS) : TempForm::select(TempForm::TABLE_COLUMNS);

        // dd($this->whereIn);
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
            'requester' => function($query) {
                $query->select(User::MINIMAL_COLUMNS);
            },        
            'employee' => function($query) {
                $query->select(User::MINIMAL_COLUMNS);
            },                    
            'template' => function($query) {
                $query->select(FormTemplate::MINIMAL_COLUMNS);
            },
            'approvers' => function($query) {
                $query->select(TempFormApprover::MINIMAL_COLUMNS);
            },
            'ticket' => function($query) {
                $query->select(Ticket::MINIMAL_COLUMNS);
            },             
        ];
    }

    /**
     * Fetches all employees that is handled by the current user
     */    
    public function getEmployees() {

        /* Fetch all included employee IDs */
        $employeeIDs = $this->getIncludedEmployeeIDs(); //dd($employeeIDs);


        /* Set orWhereIn variables */
        array_push($this->whereIn, [
            'column' => 'temp_forms.id',
            'array' => $employeeIDs
        ]);
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
                return TempForm::withTrashed()->get()->pluck('id')->toArray();


            /* Fetch company */
            $company = Company::withTrashed()->findOrFail($tmpCompany);


            foreach($company->divisions as $division) {
                foreach($division->departments as $department) {
                    foreach($department->employees as $employee) {

                        /* Merge the form IDs */
                        if($employee->employee)
                            $ids = array_merge($ids, $employee->employee->temp_forms()->pluck('id')->toArray());
                    }
                }
            }
        }

        /* Fetch ticket that is assigned to the current user */
        $ids = array_merge($ids, $this->user->update_approvals()->pluck('temp_form_id')->toArray());

        /* Include self */
        $ids = array_merge($ids, $this->user->temp_forms()->pluck('id')->toArray());

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
     * Display the specified resource's answers.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function fetchAnswer($id)
    {
        $form = TempForm::withTrashed()->findOrFail($id);


        /* Fetch rendered answers */
        $answers = $form->renderAnswers();

        return response()->json([
            'answers' => $answers,
        ]);
    }
}
