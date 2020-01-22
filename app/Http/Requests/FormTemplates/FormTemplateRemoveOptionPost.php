<?php

namespace App\Http\Requests\FormTemplates;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Factory as ValidationFactory;

use Route;
use App\FormTemplate;
use App\FormTemplateField;
use App\FormTemplateOption;

class FormTemplateRemoveOptionPost extends FormRequest
{
    protected $formTemplate;
    protected $formTemplateField;

    public function __construct(ValidationFactory $validationFactory)
    {
        $route = Route::current()->getName();

        $this->formTemplateField = FormTemplateField::find(Route::current()->parameter('id'));
        $this->formTemplate = $this->formTemplateField->form_template;


        /* For the checking of meeting room validation */
        $validationFactory->extend('sla_settings', function($attribute, $value, $parameters) {
                /* Check if SLA settings is pointed on a table */
                if($this->formTemplateField->isSLADateOnTable()) {
                    /* Check if SLA settings is on this column */
                    if($this->formTemplate->sla_col_id == $value)
                        return false;
                }

                return true;
            }, 
            "Option field cannot be deleted as its being used as the starting date for the form's SLA"
        );        
    }


    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if(\Auth::check()) {

            /* Check if user has permission */
            if(\Auth::user()->hasRole('Creating/Designing/Editing/Removing of Forms'))
                return true;
        }

        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'form_template_option_id' => 'required|exists:form_template_options,id|sla_settings',
        ];
    }

    /**
     * Get the error messages.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'form_template_option_id.required' => 'Form Template option not found',
            'form_template_option_id.exists' => 'Form Template option not found',            
        ];
    }      
}
