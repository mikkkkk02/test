<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportPost extends FormRequest
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
            'file' => 'required',
            // @TODO: Add in mime validation
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
            'file.required' => "No files uploaded",
            'file.mimes' => "File uploaded should be an excel file",
        ];
    }     
}
