<?php

namespace App\Http\Requests\Forms;

use Illuminate\Foundation\Http\FormRequest;

class FormAddUpdatePost extends FormRequest
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
            'description' => 'required',
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
            'description.required' => 'Please input the description',
        ];
    }
}
