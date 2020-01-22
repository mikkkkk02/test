<?php

namespace App\Http\Requests\Divisions;

use Illuminate\Foundation\Http\FormRequest;

class DivisionAssignCompanyPost extends FormRequest
{
    /**
     * Determine if the Role is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return \Auth::check() && \Auth::Role()->hasRole('Administrator');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'company_id' => 'required|exists:companys,id',
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
            'company_id.required' => 'company not found',
            'company_id.exists' => 'company not found',
        ];
    }
}
