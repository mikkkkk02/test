<?php

namespace App\Http\Requests\FormTemplates;

use Illuminate\Foundation\Http\FormRequest;

class FormTemplateRemoveFieldPost extends FormRequest
{
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
            //
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
            //
        ];
    }      
}
