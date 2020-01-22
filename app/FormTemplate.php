<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Laravel\Scout\Searchable;

class FormTemplate extends Model
{
    use SoftDeletes;
    use Searchable;

    public $asYouType = true;

    /*
    |-----------------------------------------------
    | @Columns
    |-----------------------------------------------
    */
    const MINIMAL_COLUMNS = [
        'id', 'form_template_category_id', 'travel_order_table_id',
        'enableAttachment', 'enableManagerial',
        'type', 'request_type', 'approval_option',
        'name', 'sla'
    ];

    const TABLE_COLUMNS = [
        'id', 'form_template_category_id',
        'type', 'request_type', 'name', 'description', 'sla',
        'enableAttachment', 'enableManagerial',
        'policy', 'sla_text',
        'updated_at', 'deleted_at',
        'updater_id', 
    ];

    /*
    |-----------------------------------------------
    | @Type
    |-----------------------------------------------
    */
    const ADMIN = 0;
    const HR = 1;
    const OD = 2;

    /*
    |-----------------------------------------------
    | @Priority
    |-----------------------------------------------
    */
    const LOW = 0;
    const MEDIUM = 1;
    const HIGH = 2;

    /*
    |-----------------------------------------------
    | @Request Type
    |-----------------------------------------------
    */
    const NA = 0;
    const TRAVELORDER = 1;
    const MEETINGROOM = 2;

    /*
    |-----------------------------------------------
    | @Approval Option
    |-----------------------------------------------
    */
    const INORDER = 0;
    const SIMULTANEOUSLY = 1;

    const MANAGERIAL = 1;


    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    protected $guarded = [];
    protected $appends = ['extra'];


    public function category() {
    	return $this->belongsTo(FormTemplateCategory::class, 'form_template_category_id');
    }

    public function contacts() {
    	return $this->hasMany(FormTemplateContact::class);
    }

    public function approvers() {
        return $this->hasMany(FormTemplateApprover::class);
    }

    public function fields() {
    	return $this->hasMany(FormTemplateField::class)->orderBy('sort');
    }

    public function travel_order_table() {
        return $this->belongsTo(FormTemplateField::class, 'travel_order_table_id');
    }

    public function forms() {
    	return $this->hasMany(Form::class);
    }

    public function mr_reservations() {
        return $this->hasMany(MrReservation::class, 'form_template_id');
    }

    public function creator() {
        return $this->belongsTo(User::class, 'creator_id')->withTrashed();
    }

    public function updater() {
        return $this->belongsTo(User::class, 'updater_id')->withTrashed();
    } 


    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        $searchable = [
            'id' => $this->id,
            'name' => $this->name,
            'priority' => $this->renderPriority(),
            'type' => $this->renderType(),
            'sla' => $this->sla,
        ];

        return $searchable;
    }


    /*
    |-----------------------------------------------
    | @Helper
    |-----------------------------------------------
    */
    public function getExtraAttribute() {
        return $this->attributes['extra'] = [
            'priority' => $this->renderPriority(),
            'type' => $this->renderType(),
            'create' => $this->renderCreateURL(),
            'view' => $this->renderViewURL(),
        ];
    }    

    public function setPriorityAsLow($save = false) {
        $this->priority = FormTemplate::LOW;

        if($save)
            $this->save();
    }

    public function setPriorityAsMedium($save = false) {
        $this->priority = FormTemplate::MEDIUM;

        if($save)
            $this->save();        
    }

    public function setPriorityAsHigh($save = false) {
        $this->priority = FormTemplate::HIGH;

        if($save)
            $this->save();
    }

    public function getValidationRules($fields = []) {
        $rules = [];

        if (!$fields) {
            $fields = $this->fields;
        }

        /* Loop each form template fields */
        foreach($fields as $field) {

            /* Check if field needs checking */
            if($field->isRequired) {


                /*
                | @Create the validation rules
                |-----------------------------------------------*/

                /* Add in required validation */
                $validations = 'required';

                /* Add in other validation */
                switch($field->type) {
                    case FormTemplateField::DATEFIELD:
                        
                        $validations .= '|date_format:"Y-m-d"';

                    break;
                    case FormTemplateField::TABLE:
                        

                        /*
                        | @Create the validation rules for each column
                        |-----------------------------------------------*/

                        $rules['fields.' . $field->id . '.*'] = ['required'];

                        /* Loop through the field column */
                        foreach($field->options as $option) {

                            /* Add in option validation */
                            $optValidations = 'required';

                            /* Add in other option validation */
                            switch($option->type) {
                                case FormTemplateOption::DATEFIELD:
                                    
                                    $optValidations .= '|date_format:"Y-m-d"';

                                break;
                                case FormTemplateOption::NUMBER:

                                    $optValidations .= '|numeric';

                                break;
                            }            
                            
                            $rules['fields.' . $field->id . '.*.' . $option->id] = $optValidations;
                        }

                    break;                    
                }

                /* Add in to the rules array except for table fields */
                if($field->type != FormTemplateField::TABLE)
                    $rules['fields.' . $field->id] = $validations;
            }
        }

        return $rules;
    }

    public function getValidationMessages() {
        $messages = [];


        /* Loop each form template fields */
        foreach($this->fields as $field) {

            /* Check if field needs checking */
            if($field->isRequired) {


                /*
                | @Create the validation messages
                |-----------------------------------------------*/

                /* Add in required message */
                $messages['fields.' . $field->id . '.required'] = ['Please input the ' . $field->label];

                /* Add in other messages */
                switch($field->type) {
                    case FormTemplateField::DATEFIELD:
                        
                        $messages['fields.' . $field->id . '.date_format'] = ['The ' . $field->label . ' does not match the required format (1990-01-01)'];

                    break;
                    case FormTemplateField::TABLE:
                        

                        /*
                        | @Create the validation messages for each column
                        |-----------------------------------------------*/

                        /* Loop through the field column */
                        foreach($field->options as $option) {

                            /* Add in option required message */
                            $messages['fields.' . $field->id . '.*.required'] = ['Please input the ' . $field->label];
                            $messages['fields.' . $field->id . '.*.' . $option->id . '.required'] = ['Please input the ' . $option->value];

                            /* Add in other option validation */
                            switch($option->type) {
                                case FormTemplateOption::DATEFIELD:
                                    
                                    $messages['fields.' . $field->id . '.*.' . $option->id . '.date_format'] = ['The ' . $option->value . ' does not match the required format (1990-01-01)'];

                                break;
                                case FormTemplateOption::NUMBER:

                                    $messages['fields.' . $field->id . '.*.' . $option->id . '.numeric'] = ['The ' . $option->value . ' must be a number'];

                                break;
                            }
                        }

                    break;
                }
            }
        }

        return $messages;
    }

    public static function getPriorities() {
        return [
            ['label' => 'Low', 'value' => FormTemplate::LOW],
            ['label' => 'Medium', 'value' => FormTemplate::MEDIUM],
            ['label' => 'High', 'value' => FormTemplate::HIGH],
        ];
    }

    public static function getType() {
        return [
            ['label' => 'Admin', 'value' => FormTemplate::ADMIN],
            ['label' => 'HR', 'value' => FormTemplate::HR],
            ['label' => 'OD', 'value' => FormTemplate::OD],
        ];
    }

    public static function getRequestType() {
        return [
            ['label' => 'NA', 'value' => FormTemplate::NA],
            ['label' => 'Travel Order', 'value' => FormTemplate::TRAVELORDER],
            ['label' => 'Meeting Room', 'value' => FormTemplate::MEETINGROOM],
        ];
    }  

    public static function getFilter() {
        $forms = [];

        foreach (FormTemplate::orderBy('name', 'asc')->get() as $key => $template) {
            array_push($forms, [
                'label' => $template->name,
                'value' => $template->id
            ]);
        }

        return $forms;
    }      


    /*
    |-----------------------------------------------    
    | @Methods
    |-----------------------------------------------
    */
    public function addContact($contact) {
        $this->contacts()->save($contact);
    }

    public function addApprover($approver) {
        $this->approvers()->save($approver);
    }

    public function addField($request) {

        /* Create variable fields for template field */
        $vars = $request->except('options');
        $vars['form_template_id'] = $this->id;
        $vars['sort'] = $this->fields()->count() + 1;

        /* Create form template field */
        return FormTemplateField::create($vars);
    }

    public function removeField($field) {

        /* Remove the field */
        $field->delete();

        /* Re-calculate field sorting */
        $this->refreshFieldSorting();
    }

    public function updateField($field, $request) {

        /* update the field */
        $field->update($request);

        /* Re-calculate field sorting */
        $this->refreshFieldSorting();
    }

    public function updateSorting($request) {

        /* Update each fields */
        foreach ($this->fields->sortBy('sort') as $key => $field) {
            $field->setSort($request[$key]);
        }

        $this->fields->pluck('sort', 'label');
    }

    public function refreshFieldSorting() {

        $count = 0;

        /* Loop throught the fields */
        foreach($this->fields as $field) {

            /* Check if sort is correct */
            if($field->sort != $count) {

                $field->sort = $count;
                $field->save();
            }

            $count++;
        }
    }    

    public function fetchAvailableTablefields($displaySelected = false, $withTables = false) {

        $tables = collect([]);

        if ($this->request_type == FormTemplate::TRAVELORDER) {
            if (!$displaySelected) {
                $tables = $this->fields()->where('type', FormTemplateField::TABLE);
            } else {
                $tables = $this->fields()->where('id', $this->travel_order_table_id);
            }

            if ($withTables) {
                $tables->with('options');
            }

            $tables = $tables->get();
        }

        return $tables;
    }

    /*
    |-----------------------------------------------
    | @Checker
    |-----------------------------------------------
    */   
    public function isTravelOrder() {
        return $this->request_type == FormTemplate::TRAVELORDER;
    } 

    public function isMeetingRoom() {
        return $this->request_type == FormTemplate::MEETINGROOM;
    }       

    public function isLowPriority() {
        return $this->priority == FormTemplate::LOW;
    }

    public function isMediumPriority() {
        return $this->priority == FormTemplate::MEDIUM;
    }

    public function isHighPriority() {
        return $this->priority == FormTemplate::HIGH;
    }

    public function isInOrder() {
        return $this->approval_option == FormTemplate::INORDER;
    }

    public function isManagerial() {
        return $this->enableManagerial == FormTemplate::MANAGERIAL;
    }  


    /*
    |-----------------------------------------------
    | @Render
    |-----------------------------------------------
    */
    public static function renderFilterArray() {
        $templates = FormTemplate::select(FormTemplate::MINIMAL_COLUMNS)->get()->sortBy('name');
        $array = [];


        /* Add in status options */
        $array[0] = [
            'label' => 'form',
            'options' => [],
        ];

        /* Store each object */
        foreach ($templates as $key => $template) {
            array_push($array[0]['options'], [
                'id' => $template->id,
                'label' => $template->name,
            ]);
        }

        return $array;
    }   

    public function renderPriority() {
        return $this->renderConstants(FormTemplate::getPriorities(), $this->priority);
    }

    public function renderType() {
        return $this->renderConstants(FormTemplate::getType(), $this->type);
    }

    public function renderRequestType() {
        return $this->renderConstants(FormTemplate::getRequestType(), $this->special);
    }

    public function renderConstants($array, $value) {

        /* Loop through the array */
        foreach ($array as $obj) {
            
            if($obj['value'] == $value)
                return $obj['label'];
        }
    }   

    public function renderCreateURL() {
        return route('request.create', $this->id);
    }   

    public function renderViewURL() {
        return route('formtemplate.show', $this->id);
    }        
}
