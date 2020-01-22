<?php

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;

use Route;

class UserStorePost extends FormRequest
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
        $userID = Route::current()->parameter('id');

        return [
            'email' => 'nullable|email|unique:users,email,' . $userID,

            // 'changePassword' => 'required',

            // 'password' => 'required|confirmed|min:6',

            'groups.*' => 'nullable|exists:groups,id',
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
            'email.unique' => "Email address already used",

            // 'changePassword.required' => 'Change password toggle missing',

            // 'password.required' => "Please enter the password",
            // 'password.confirmed' => "Password doesn't match",
            // 'password.min' => "Password should be more than 6 letter/digits",

            'groups.*.exists' => 'Group not found',
        ];
    }    
}
