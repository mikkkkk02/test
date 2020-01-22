<?php

namespace App\Http\Requests\Tickets;

use Illuminate\Foundation\Http\FormRequest;

class TicketAttachmentPost extends FormRequest
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
            'attachment' => 'nullable|max:2000',
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
            'attachment.max' => "File size must be less than 2mb",            
        ];
    }
}
