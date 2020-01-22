<?php

namespace App\Http\Requests\Learnings;

use Illuminate\Foundation\Http\FormRequest;

class LearningAddParticipantPost extends FormRequest
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
            'participant_id' => 'required|exists:users,id',

            'charge_to' => 'required|exists:departments,id',
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
            'participant_id.required' => 'Participant not found',
            'participant_id.exists' => 'Participant not found',

            'charge_to.required' => 'Department not found',
            'charge_to.exists' => 'Department not found',            
        ];
    }  
}
