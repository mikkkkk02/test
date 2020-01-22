<?php

namespace App\Http\Requests\FormTemplates;

use Illuminate\Foundation\Http\FormRequest;

class FormTemplateFieldUpdatePost extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return \Auth::check() && \Auth::user()->hasRole('Administrator');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'form_template_id' => 'required|exists:form_templates,id',

            'sort' => 'required|numeric',
            'label' => 'required',

            'type' => 'required|min:0|max:6',
            'type_value' => 'required_if:type,0',

            'isRequired' => 'boolean',

            'options' => 'required_if:type,4,5,6',
            'options.*' => 'required_if:type,4,5,6',
            'options.*.value' => 'required_if:type,4,5,6',
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
            'form_template_id.required' => 'Form Template not found',
            'form_template_id.exists' => 'Form Template not found',

            'sort.required' => 'Invalid sort number',
            'sort.numeric' => 'Invalid sort number',

            'label.required' => 'Please input the field label',

            'type.required' => 'Please select the field type',
            'type.min' => 'Invalid type',
            'type.max' => 'Invalid type',
            'type_value.required_if' => 'Invalid type value',

            'isRequired.boolean' => 'Invalid required field',            

            'options.required' => 'Please add in the option fields',
            'options.*.required' => 'Please add in the option fields',
            'options.*.value.required' => 'Please input the option fields value',
        ];
    }      
}
