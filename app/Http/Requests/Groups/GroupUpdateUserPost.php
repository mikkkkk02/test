<?php

namespace App\Http\Requests\Groups;

use Illuminate\Foundation\Http\FormRequest;

class GroupUpdateUserPost extends FormRequest
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
            'users.*.required' => 'User not found',            
            'users.*.exists' => 'User not found',
        ];
    }    
}
