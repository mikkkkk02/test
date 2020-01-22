<?php

namespace App\Http\Requests\FormTemplates;

use Illuminate\Foundation\Http\FormRequest;

class FormTemplateStorePost extends FormRequest
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
        return [
            'form_template_category_id' => 'required|exists:form_template_categories,id',

            'name' => 'required',

            'priority' => 'required|min:0|max:2',
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

            'priority.required' => 'Please select the priority',
            'priority.min' => 'Invalid priority',
            'priority.max' => 'Invalid priority',
        ];
    }      
}
