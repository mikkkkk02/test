<?php

namespace App\Http\Requests\FormTemplates;

use Illuminate\Foundation\Http\FormRequest;

class FormTemplateAddContactPost extends FormRequest
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
            'type' => 'required|min:1|max:3',

            'employee_id' => 'required_if:type,2',
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
            'type.required' => 'Please select the type',
            'type.min' => 'Invalid type',
            'type.max' => 'Invalid type',

            'employee_id.required_if' => 'Contact not found',
        ];
    }      
}
