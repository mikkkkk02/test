<?php

namespace App\Http\Requests\Employees;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeUpdateSettingsPost extends FormRequest
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
            'employees.*' => 'nullable|exists:users,id',

            'vacation_proxy_id' => 'required_if:onVacation, 1|exists:users,id',

            'vacation_start_date' => 'required_if:onVacation, 1|date_format:"Y-m-d"',
            'vacation_end_date' => 'required_if:onVacation, 1|after_or_equal:vacation_start_date|date_format:"Y-m-d"',
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
            'employees.*.exists' => 'User not found',

            'vacation_proxy_id.required_if' => 'Employee not found',
            'vacation_proxy_id.exists' => 'Employee not found',

            'vacation_start_date.required_if' => 'Please input the start date',
            'vacation_start_date.date_format' => "The start date does not match the required format (1990-01-01)",

            'vacation_end_date.required_if' => 'Please input the end date',
            'vacation_end_date.after_or_equal' => "The end date must be less than or equal to the start date",
            'vacation_end_date.date_format' => "The end date does not match the required format (1990-01-01)",
        ];
    }      
}
