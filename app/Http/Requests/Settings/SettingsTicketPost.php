<?php

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;

class SettingsTicketPost extends FormRequest
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
            'admin_technician_id.*' => 'exists:users,id',
            'hr_technician_id.*' => 'exists:users,id',
            'od_technician_id.*' => 'exists:users,id',
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
            'admin_technician_id.*.exists' => 'User not found',
            'hr_technician_id.*.exists' => 'User not found',
            'od_technician_id.*.exists' => 'User not found',
        ];
    }  
}
