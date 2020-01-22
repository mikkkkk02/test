<?php

namespace App\Http\Requests\Positions;

use Illuminate\Foundation\Http\FormRequest;

class PositionStorePost extends FormRequest
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
            'department_id' => 'required|exists:departments,id',
            
            'title' => 'required',
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
            'department_id.required' => 'Department not found',
            'department_id.exists' => 'Department not found',

            'title.required' => 'Please input the position title',
        ];
    }      
}
