<?php

namespace App\Http\Requests\Events;

use Illuminate\Http\Request;
use Illuminate\Foundation\Http\FormRequest;

use Carbon\Carbon;

class EventRemoveParticipantPost extends FormRequest
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
    public function rules(Request $request)
    {
        return [
            'participants.*' => 'required|exists:event_participants,id',
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
            'participants.required' => 'Participants not found',
            'participants.exists' => 'Participants not found',
        ];
    }
}
