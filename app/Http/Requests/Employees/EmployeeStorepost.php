<?php

namespace App\Http\Requests\Employees;

use Illuminate\Foundation\Http\FormRequest;

use Route;

class EmployeeStorePost extends FormRequest
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
        $userID = Route::current()->parameter('id');

        return [
            'id' => 'nullable|unique:users,id,' . $userID,

            'employee_category_id' => 'required|exists:employee_categories,id',

            'department_id' => 'nullable|exists:departments,id',
            'position_id' => 'required_if:department_id,!nullable|exists:positions,id',
            'supervisor_id' => 'nullable|exists:users,id',
            'location_id' => 'required|exists:locations,id',

            'first_name' => 'required|alpha_space',
            'middle_name' => 'nullable|alpha_space',
            'last_name' => 'required|alpha_space',
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
            'id.required' => "Please enter the assignment no.",
            'id.unique' => "Assignment no. already used",

            'employee_category_id.required' => "Employee Category not found",
            'employee_category_id.exists' => "Employee Category not found",

            'department_id.exists' => "Department not found",

            'position_id.required' => "Position not found",
            'position_id.exists' => "Position not found",

            'supervisor_id.required' => "Supervisor not found",
            'supervisor_id.exists' => "Supervisor not found", 

            'location_id.required' => "Location not found",
            'location_id.exists' => "Location not found",

            'first_name.required' => "Please enter the First name",
            'first_name.alpha_space' => "First name must only contain letters",
            'middle_name.alpha_space' => "Middle name must only contain letters",
            'last_name.required' => "Please enter the Last name",
            'last_name.alpha_space' => "Last name must only contain letters",
        ];
    }      
}
