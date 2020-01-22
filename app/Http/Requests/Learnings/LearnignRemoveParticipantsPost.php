<?php

namespace App\Http\Requests\Learnings;

use Illuminate\Foundation\Http\FormRequest;

class LearningRemoveParticipantsPost extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return \Auth::check() && \Auth::user()->hasRole('Administrator');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'users.*' => 'required|exists:users,id',
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
            'users.*.exists' => 'Employee not found',
        ];
    }    
}
