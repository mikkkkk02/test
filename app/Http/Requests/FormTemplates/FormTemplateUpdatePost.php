<?php

namespace App\Http\Requests\FormTemplates;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

use Validator;

use App\FormTemplate;

class FormTemplateUpdatePost extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return \Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $formTemplate = FormTemplate::find($this->id);

        return [
            'form_template_category_id' => 'required|exists:form_template_categories,id',

            'name' => 'required',

            'sla' => 'required|numeric',
            'sla_option' => 'nullable|min:0|max:1',
            'sla_type' => 'nullable|min:0|max:1',
            'sla_date_id' => 'required_if:sla_option,1|exists:form_template_fields,id',
            'sla_col_id' => 'required_if:sla_type,1|exists:form_template_options,id',
            'travel_order_table_id' => 'nullable|' . Rule::in($formTemplate->fetchAvailableTablefields()->pluck('id')->toArray()),

            'priority' => 'required|numeric|min:0|max:2',
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
            'form_template_category_id.required' => 'Form Category not found',
            'form_template_category_id.exists' => 'Form Category not found',

            'name.required' => 'Please input the form template name',

            'sla.required' => 'Please input the form template SLA',
            'sla.numeric' => 'SLA must be a number',

            'sla_option.min' => 'Invalid SLA option',
            'sla_option.max' => 'Invalid SLA option',

            'sla_date_id.required_if' => 'Please select the date field for the SLA',
            'sla_date_id.exists' => 'Invalid SLA date field option',

            'travel_order_table_id.in' => 'Invalid Travel Order table',

            'priority.required' => 'Please select the priority',
            'priority.min' => 'Invalid priority',
            'priority.max' => 'Invalid priority',
        ];
    }      
}
