<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormTemplateField extends Model
{
    use SoftDeletes; 
    
    /*
    |-----------------------------------------------
    | @Columns
    |-----------------------------------------------
    */
    const MINIMAL_COLUMNS = [
        'id', 'form_template_id',
        'sort', 'label', 'type', 'type_value',
        'hasOthers', 'isRequired',
    ];

    /*
    |-----------------------------------------------
    | @Type
    |-----------------------------------------------
    */
    const TEXTFIELD = 0;
    const TEXTAREA = 1;
    const DATEFIELD = 2;
    const RADIOBOX = 3;
    const CHECKBOX = 4;
    const TABLE = 5;
    const DROPDOWN = 6;
    const HEADER = 7;
    const PARAGRAPH = 8;
    const TIME = 9;


    protected $guarded = [];
    protected $appends = ['extra'];


    public function form_template() {
    	return $this->belongsTo(FormTemplate::class)->withTrashed();
    }

    public function options() {
        return $this->hasMany(FormTemplateOption::class)->orderBy('sort');
    }

    public function answers() {
        return $this->hasMany(FormAnswer::class);
    }

    public function travel_order_template() {
        return $this->hasOne(FormTemplate::class, 'travel_order_table_id');
    }

    public function travel_order_details() {
        return $this->hasMany(TicketTravelOrderDetail::class, 'form_template_field_id');
    }


    /*
    |-----------------------------------------------
    | @Helper
    |-----------------------------------------------
    */
    public function getExtraAttribute() {
        return $this->attributes['extra'] = [
            'type' => $this->renderType(),
            'optionurl' => $this->renderOptionURL(),
            'optionremoveurl' => $this->renderOptionRemoveURL(),
            'updateurl' => $this->renderUpdateURL(),
            'deleteurl' => $this->renderDeleteURL(),
        ];
    }   

    public function setSort($index) {
        $this->sort = $index;
        $this->save();
    }    

    public function refreshOptionSorting() {

        $count = 1;

        /* Loop throught the options */
        foreach($this->options as $option) {

            /* Check if sort is correct */
            if($option->sort != $count) {

                $option->sort = $count;
                $option->save();
            }

            $count++;
        }
    }

    public static function getTypes() {
        return [
            ['label' => 'Textfield', 'value' => FormTemplateField::TEXTFIELD],
            ['label' => 'Textarea', 'value' => FormTemplateField::TEXTAREA],
            ['label' => 'Datefield', 'value' => FormTemplateField::DATEFIELD],
            ['label' => 'Radiobox', 'value' => FormTemplateField::RADIOBOX],
            ['label' => 'Checkbox', 'value' => FormTemplateField::CHECKBOX],
            ['label' => 'Table', 'value' => FormTemplateField::TABLE],
            ['label' => 'Dropdown', 'value' => FormTemplateField::DROPDOWN],
            ['label' => 'Header', 'value' => FormTemplateField::HEADER],
            ['label' => 'Paragraph', 'value' => FormTemplateField::PARAGRAPH],
            ['label' => 'Time', 'value' => FormTemplateField::TIME],
        ];
    }   


    /*
    |-----------------------------------------------
    | @Methods
    |-----------------------------------------------
    */
    public function updateField($request) {

        /* Chech if current field is the SLA start date */
        $isSLA = $this->isSLADate() && !$this->isSLADateOnTable();

        /* Check if SLA settings is on this column */
        if($isSLA && (FormTemplateField::DATEFIELD != $request['type'] || !$request['isRequired']))
            return false;


        /* Update form template field */
        $this->update($request);

        return true;
    }

    public function addOption($request) {

        /* Create variable field for template option */
        $vars = $request->all();
        $vars['form_template_field_id'] = $this->id;
        $vars['sort'] = $this->options()->count() + 1;


        /* Create form template option */
        return FormTemplateOption::create($vars);
    }

    public function removeOption($option) {

        /* Remove the option */
        $option->delete();

        /* Re-calculate option sorting */
        $this->refreshOptionSorting();
    }

    public function updateOptions($options) {

        /* Chech if current field is the SLA start date */
        $isSLA = $this->isSLADateOnTable();


        /* Update each options */
        foreach ($this->options as $key => $option) {

            /* Update/Delete option */
            if(isset($options[$key])) {

                /* Fetch & create unneeded vars array */
                $remove = ['extra'];

                /* Remove unneeded vars */
                $vars = array_diff_key($options[$key], array_flip($remove));
                
                /* Check if SLA settings is on this column */
                if($isSLA && $this->form_template->sla_col_id == $vars['id']) {
                    /* Check if type is still date */
                    if(FormTemplateOption::DATEFIELD != $vars['type']) {
                        return false;
                    }
                }

                $option->update($vars);

            } else {
                $option->delete();
            }
        }

        return true;
    }


    /*
    |-----------------------------------------------
    | @Checker
    |-----------------------------------------------
    */
    public function isSLADate() {
        return $this->form_template->sla_option && $this->form_template->sla_date_id == $this->id;
    }   

    public function isSLADateOnTable() {
        return $this->isSLADate() && $this->form_template->sla_type == 1;
    }

    public function isTravelOrderDetailTable() {
        return $this->travel_order_details()->count();
    }    

    public function isDatefield() {
        return $this->type == FormTemplateField::DATEFIELD;
    }    

    public function isTable() {
        return $this->type == FormTemplateField::TABLE;
    }    


    /*
    |-----------------------------------------------
    | @Render
    |-----------------------------------------------
    */   
    public function renderType() {
        return $this->renderConstants(FormTemplateField::getTypes(), $this->type);
    }

    public function renderConstants($array, $value) {

        /* Loop through the array */
        foreach ($array as $obj) {
            
            if($obj['value'] == $value)
                return $obj['label'];
        }
    }

    public function renderOptionURL() {
        return route('formtemplatefield.addoption', $this->id);
    }

    public function renderOptionRemoveURL() {
        return route('formtemplatefield.removeoption', $this->id);
    }    

    public function renderUpdateURL() {
        return route('formtemplatefield.edit', $this->id);
    }

    public function renderDeleteURL() {
        return route('formtemplatefield.delete', $this->id);
    }        
}
