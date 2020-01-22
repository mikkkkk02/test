<?php

namespace App\Http\Requests\Divisions;

use Illuminate\Foundation\Http\FormRequest;

class DivisionStorePost extends FormRequest
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
            'company_id' => 'required|exists:companies,id',

            'name' => 'required',
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
            'company_id.required' => 'Company not found',
            'company_id.exists' => 'Company not found',

            'name.required' => 'Please input the group name',
        ];
    }      
}
