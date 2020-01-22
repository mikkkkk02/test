<?php

namespace App\Http\Requests\FormTemplates;

use Illuminate\Foundation\Http\FormRequest;

class FormTemplateRemoveApproverPost extends FormRequest
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
            'id' => 'required|exists:form_template_approvers,id',
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
            'id.required' => 'Approver not found',
            'id.exists' => 'Approver not found',
        ];
    }      
}
