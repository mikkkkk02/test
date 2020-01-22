<?php

namespace App\Http\Requests\MrReservations;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Http\Requests\Forms\FormStorePost;

use App\FormTemplate;

class MrReservationStorePost extends FormRequest
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
        $this->formTemplate = FormTemplate::findOrFail($this->input('form_template_id'));

        $ids = FormTemplate::where('request_type', FormTemplate::MEETINGROOM)->pluck('id')->toArray();


        return array_merge(parent::rules(), [
            'form_template_id' => 'required|' . Rule::in($ids),
            'mrreservationtime.*.date' => 'required|date',
            'mrreservationtime.*.start_time' => 'required|date_format:H:i',
            'mrreservationtime.*.end_time' => 'required|date_format:H:i',
        ]);
    }

    /**
     * Get the error messages.
     *
     * @return array
     */
    public function messages()
    {
        return array_merge(parent::messages(), [
            'form_template_id.required' => 'Form template is required',
            'form_template_id.in' => 'Form template is not a meeting room',
            'mrreservationtime.*.date.required' => 'Date is required',
            'mrreservationtime.*.date.date' => 'Date is invalid format',
            'mrreservationtime.*.start_time.required' => 'Start time is required',
            'mrreservationtime.*.start_time.date_format' => 'Start time is invalid format',
            'mrreservationtime.*.end_time.required' => 'End time is required',
            'mrreservationtime.*.end_time.date_format' => 'End time is invalid format',
        ]);
    }
}
