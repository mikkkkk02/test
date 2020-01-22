<?php

namespace App\Http\Requests\Learnings;

use Illuminate\Foundation\Http\FormRequest;

class LearningRemoveParticipantPost extends FormRequest
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
        ];
    }  
}
