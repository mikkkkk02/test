<?php

namespace App\Http\Requests\FormTemplates;

use Illuminate\Foundation\Http\FormRequest;

use Validator;

class FormTemplateUpdateSortingPost extends FormRequest
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
            'sorting.*' => 'required|numeric',
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
            'sorting.required' => 'Invalid sorting',
            'sorting.*.numeric' => 'Invalid sorting',
        ];
    }      
}
