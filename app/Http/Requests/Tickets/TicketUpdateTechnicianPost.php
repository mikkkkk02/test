<?php

namespace App\Http\Requests\Tickets;

use Illuminate\Foundation\Http\FormRequest;

class TicketUpdateTechnicianPost extends FormRequest
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
            'technician_id' => 'required|exists:users,id',
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
            'technician_id.required' => 'Technician not found',
            'technician_id.exists' => 'Technician not found',
        ];
    }
}
