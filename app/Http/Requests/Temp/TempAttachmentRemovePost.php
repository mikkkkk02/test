<?php

namespace App\Http\Requests\Temp;

use Illuminate\Foundation\Http\FormRequest;

class TempAttachmentRemovePost extends FormRequest
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
            'attachment_id' => 'required|exists:temp_attachments,id',
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
            'attachment_id.required' => 'Attachment not found',
            'attachment_id.exists' => 'Attachment not found',
        ];
    }
}
