<?php

namespace App\Http\Requests\Learnings;

use Illuminate\Foundation\Http\FormRequest;

use App\Idp;

class IDPStorePost extends FormRequest
{
    /**
     * Determine if the Role is authorized to make this request.
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
            'employee_id' => 'required|exists:users,id',
            'competency_id' => 'required|exists:idp_competencies,id',

            'learning_type' => 'required|min:' . Idp::EDUCATION . '|max:' . Idp::EXPOSURE,
            'competency_type' => 'required|min:' . Idp::TECHNICAL . '|max:' . Idp::LEADERSHIP,

            'required_proficiency_level' => 'required|min:' . Idp::MINPROFICIENCY . '|max:' . Idp::MAXPROFICIENCY,
            'current_proficiency_level' => 'required|min:' . Idp::MINPROFICIENCY . '|max:' . Idp::MAXPROFICIENCY,

            'type' => 'required|min:' . Idp::NONE . '|max:' . Idp::ADDCOMPETENCY,
            'status' => 'required|min:' . Idp::ONGOING . '|max:' . Idp::COMPLETED,

            'completion_year' => 'required|date_format:"Y"',
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
            'employee_id.required' => "Employee not found",
            'employee_id.exists' => "Employee not found",

            'competency_id.required' => "Please input the Specific Competency",
            'competency_id.min' => "Invalid Specific Competency",
            'competency_id.max' => "Invalid Specific Competency",

            'learning_type.required' => "Please input the Learning Activity type",
            'learning_type.min' => "Invalid Learning Activity type",            
            'learning_type.max' => "Invalid Learning Activity type",            

            'competency_type.required' => "Please input the Competency Activity type",
            'competency_type.min' => "Invalid Competency Activity type",
            'competency_type.max' => "Invalid Competency Activity type",

            'required_proficiency_level.required' => "Please input the required proficiency level",
            'required_proficiency_level.min' => "Invalid required proficiency level",
            'current_proficiency_level.min' => "Invalid current proficiency level",

            'type.required' => "Please input the type",            
            'type.min' => "Invalid type",            
            'type.max' => "Invalid type", 

            'status.required' => "Please input the status",            
            'status.min' => "Invalid status",            
            'status.max' => "Invalid status", 

            'completion_year.required' => "Please input the completion year",
            'completion_year.date_format' => "Invalid completion year",
        ];
    }
}
