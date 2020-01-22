<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\FormTemplate;
use App\FormTemplateContact;
use App\FormTemplateField;
use App\FormTemplateOption;
use App\FormTemplateApprover;
use App\FormTemplateCategory;
use App\Form;
use App\User;

class FormTemplateFetchController extends Controller
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

    protected $sort = 'name';
    protected $sortDir = 'asc';
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
        $templates = $this->fetchQuery($request, $this->where);
        // http_response_code(500); dd($templates);


        /* Do the pagination */
        $pagination = $templates ? $templates->paginate($this->limit) : array_merge($templates, ['data' => []]);

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
        if($request->has('category'))
            array_push($this->where, ['form_template_category_id', $request->category]);


        /* Set WhereIn */
        if($request->has('archive')) {
            array_push($this->whereIn, [
                'column' => 'id',
                'array' => FormTemplate::onlyTrashed()->get()->pluck('id')->toArray()
            ]);
        }

        if($request->has('search')) {
            array_push($this->whereIn, [
                'column' => 'id',
                'array' => FormTemplate::search($request->search)->get()->pluck('id')->toArray()
            ]);
        }


        /* Set Sort */
        if($request->has('sort')) {
            $sort = $request->input('sort');

            switch ($request->input('sort')) {
                case 'name': case 'sla': case 'priority':
                    $this->sort = $sort;
                break;
            }

            /* Set order by */
            if($request->has('order'))
                $this->sortDir = $request->input('order');
        }


        /* Fetch included templates depending on the current users permission */
        $this->getFormTemplates();
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
        $select = $request->has('archive') ? FormTemplate::withTrashed()->select(FormTemplate::TABLE_COLUMNS) : FormTemplate::select(FormTemplate::TABLE_COLUMNS);


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
            'category' => function($query) {
                $query->select(FormTemplateCategory::MINIMAL_COLUMNS);
            },
            'updater' => function($query) {
                $query->select(User::MINIMAL_COLUMNS);
            },                                    
        ];
    }

    /**
     * Fetches all form templates that is handled by the current user
     */    
    public function getFormTemplates() {

        /* Fetch all included form templates IDs */
        $templateIDs = $this->getIncludedTemplateIDs();


        /* Set orWhereIn variables */
        array_push($this->whereIn, [
            'column' => 'id',
            'array' => $templateIDs
        ]);
    }

    /**
     * Fetches all form templates IDs handled by the user's role
     */ 
    protected function getIncludedTemplateIDs() {

        /* Set necessary data */  
        $templates = [];


        foreach ($this->user->groups as $key => $group) {
            
            if($group->hasRole('Creating/Designing/Editing/Removing of Forms')) {

                /* If one group has access to all return immediately */
                if($group->type == 0)
                    return FormTemplate::withTrashed()->select(FormTemplate::MINIMAL_COLUMNS)
                                        ->get()
                                        ->pluck('id')
                                        ->toArray();


                /* Fetch templates */
                $ids = FormTemplate::withTrashed()->select(FormTemplate::MINIMAL_COLUMNS)
                                    ->where('type', ($group->type - 1))
                                    ->get()
                                    ->pluck('id')
                                    ->toArray();

                $templates = array_merge($templates, $ids);
            }
        }

        return $templates;
    }

    /**
     * Fetch all company IDs the current user has permission to 
     *
     * @return array
     */
    protected function getTemplateIDs($user, $role) {
        $templates = [];


        foreach ($user->groups as $key => $group) {
            
            if($group->hasRole($role)) {

                /* If one group has access to all return immediately */
                if($group->type == 0)
                    return FormTemplate::withTrashed()->select(FormTemplate::MINIMAL_COLUMNS)
                                        ->get()
                                        ->pluck('id')
                                        ->toArray();


                /* Fetch templates */
                $ids = FormTemplate::withTrashed()->select(FormTemplate::MINIMAL_COLUMNS)
                                    ->where('type', $group->type)
                                    ->get()
                                    ->pluck('id')
                                    ->toArray();

                array_push($templates, $ids);
            }
        }

        return $templates;
    }

    /**
     * Fetch contacts of the resource
     * 
     * @param  int  $id
     * @return \Illuminate\Http\Response 
     */
    public function fetchContacts($id) {

        $formTemplate = FormTemplate::withTrashed()->findOrFail($id);


        $contacts = $formTemplate->contacts()
                        ->select(FormTemplateContact::MINIMAL_COLUMNS)
                        ->with([
                            'employee' => function($query) {
                                $query->select(User::MINIMAL_COLUMNS);
                            },
                        ])
                        ->get();

        return response()->json([
            'response' => 1,
            'lists' => $contacts,
        ]);
    }

    /**
     * Fetch fields of the resource
     * 
     * @param  int  $id
     * @return \Illuminate\Http\Response 
     */
    public function fetchFields($id) {

        $formTemplate = FormTemplate::withTrashed()->findOrFail($id);


        $fields = $formTemplate->fields()
                            ->select(FormTemplateField::MINIMAL_COLUMNS)
                            ->with([
                                'options' => function($query) {
                                    $query->select(FormTemplateOption::MINIMAL_COLUMNS);
                                },
                            ])
                            ->get();

        return response()->json([
            'response' => 1,
            'lists' => $fields,
        ]);
    }

    /**
     * Fetch contacts of the resource
     * 
     * @param  int  $id
     * @return \Illuminate\Http\Response 
     */
    public function fetchApprovers($id) {

        $formTemplate = FormTemplate::withTrashed()->findOrFail($id);


        $approvers = $formTemplate->approvers()
                        ->select(FormTemplateApprover::MINIMAL_COLUMNS)
                        ->with([
                            'employee' => function($query) {
                                $query->select(User::MINIMAL_COLUMNS);
                            },
                        ])
                        ->get();

        return response()->json([
            'response' => 1,
            'lists' => $approvers,
        ]);
    }

    /**
     * Fetch available datefields on the resource
     * 
     * @param  int  $id
     * @return \Illuminate\Http\Response 
     */
    public function fetchAvailableDatefields($id) {

        $formTemplate = FormTemplate::withTrashed()->findOrFail($id);
        $fields = [];
        $tables = [];

        /* Loop each form template fields */
        foreach($formTemplate->fields as $key => $field) {
            /* Check if type is date */
            switch ($field->type) {
                case FormTemplateField::DATEFIELD:

                    array_push($fields, (object) [
                                            'id' => $field->id,
                                            'label' => $field->label,
                                            'type' => 0,
                                            'options' => []
                                        ]);

                break;
                case FormTemplateField::TABLE:
                    $options = [];

                    /* Check if there is a datefield text inside the table */
                    foreach ($field->options as $key => $option) {
                        if($option->type == FormTemplateOption::DATEFIELD)
                            array_push($options, (object) [
                                                    'id' => $option->id,
                                                    'label' => $option->value
                                                ]);
                    }

                    /* Add in field if there is a datefield type inside */
                    if(count($options))
                        array_push($fields, (object) [
                                                'id' => $field->id,
                                                'label' => $field->label,
                                                'type' => 1,
                                                'options' => $options
                                            ]);                        

                break;
            }
        }


        return response()->json([
            'response' => 1,
            'lists' => $fields,
        ]);
    }

    public function fetchAvailableTablefields($id)
    {
        $formTemplate = FormTemplate::withTrashed()->findOrFail($id);
        $fields = [];

        switch ($formTemplate->request_type) {
            case FormTemplate::TRAVELORDER:
                    $fields = $formTemplate->fetchAvailableTablefields();
                break;
        }


        return response()->json([
            'response' => 1,
            'lists' => $fields,
        ]);
    }

    /**
     * Display the form templates
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function fetchForms(Request $request)
    {
        $templates = FormTemplate::select(FormTemplate::TABLE_COLUMNS)
                        ->where('form_template_category_id', '!=', FormTemplateCategory::EVENT)
                        ->orderBy('name')
                        ->get();

        $formTypes = FormTemplate::getType();
        $types = [];

        foreach ($formTypes as $type) {
            $type['templates'] = $templates->where('type', $type['value']);
            array_push($types, $type);
        }

        return response()->json([
            'types' => $types,
        ]);
    }      
}
