<?php

namespace App\Http\Requests\FormTemplates;

use Illuminate\Foundation\Http\FormRequest;

class FormTemplateAddFieldPost extends FormRequest
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
            'label' => 'required',

            'type' => 'required|min:0|max:5',

            'isRequired' => 'boolean',
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
            'label.required' => 'Please input the field label',

            'type.required' => 'Please select the field type',
            'type.min' => 'Invalid type',
            'type.max' => 'Invalid type',

            'isRequired.boolean' => 'Invalid required field',            
        ];
    }      
}
