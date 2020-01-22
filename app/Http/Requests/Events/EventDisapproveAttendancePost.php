<?php

namespace App\Http\Requests\Events;

use Illuminate\Foundation\Http\FormRequest;

class EventDisapproveAttendancePost extends FormRequest
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
            'reason' => 'required',
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
            'reason.required' => 'Please input the reason',
        ];
    }
}
