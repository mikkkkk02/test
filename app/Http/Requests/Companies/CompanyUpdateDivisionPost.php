<?php

namespace App\Http\Requests\Companies;

use Illuminate\Foundation\Http\FormRequest;

class CompanyUpdateDivisionPost extends FormRequest
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
            'divisions.*' => 'required|exists:divisions,id',
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
            'divisions.*.required' => 'Group not found',            
            'divisions.*.exists' => 'Group not found',
        ];
    }    
}
