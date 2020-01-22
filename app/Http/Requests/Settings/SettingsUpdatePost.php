<?php

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;

class SettingsUpdatePost extends FormRequest
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
            'ceo_id' => 'required|exists:users,id',
            'hr_id.*' => 'exists:users,id',
            'od_id.*' => 'exists:users,id',
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
            'ceo_id.required' => 'User not found',
            'ceo_id.exists' => 'User not found',
            
            'hr_id.*.exists' => 'User not found',
            'od_id.*.exists' => 'User not found',                   
        ];
    }  
}
