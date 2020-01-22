<?php

namespace App\Http\Requests\FormModal;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Factory as ValidationFactory;
use Illuminate\Validation\Rule;

use Route;
use Carbon\Carbon;
use App\Form;
use App\FormTemplate;

class FormStorePostStep1 extends FormRequest
{
    protected $formTemplate;


    public function __construct(ValidationFactory $validationFactory) {

        $this->formTemplate = FormTemplate::find(Route::current()->parameter('id'));

    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if(\Auth::check()) {

            /* Check if exists */
            if($this->formTemplate) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {   
        /* Include required on publish only */
        if($this->has('save'))
            $rules = $this->formTemplate->getValidationRules();

        /* Add in employee rules */
        $rules['employee_id'] = 'required|exists:users,id';


        /* Add in event rules */
        $rules['event_id'] = 'nullable|exists:events,id';

        /* Add in Meeting Room rules */
        if($this->formTemplate->isMeetingRoom()) {

            $ids = FormTemplate::where('request_type', FormTemplate::MEETINGROOM)->pluck('id')->toArray();

            // $rules['mr_title'] = 'required';
            // $rules['mr_date'] = 'required|date_format:"Y-m-d"|meeting_room:mr_start_time,mr_end_time';
            // $rules['mr_start_time'] = 'required|date_format:"H:i"';
            // $rules['mr_end_time'] = 'required|date_format:"H:i"|after:mr_start_time';

            $rules['form_template_id'] = 'required|' . Rule::in($ids);
            $rules['name'] = 'required';
            // $rules['color'] = 'required';
            $rules['location_id'] = 'required';
            // $rules['room_id'] = 'required';
            $rules['mrreservationtime.*.date'] = 'required|date';
            $rules['mrreservationtime.*.start_time'] = 'required|date_format:H:i';
            $rules['mrreservationtime.*.end_time'] = 'required|date_format:H:i';
        }        


        // dd($rules);
        return $rules;
    }

    
    /**
     * Get the error messages.
     *
     * @return array
     */
    public function messages()
    {
        /* Include required on publish only */
        if($this->has('save'))        
            $messages = $this->formTemplate->getValidationMessages();

        /* Add in employee messages */
        $messages['employee_id.required'] = ['Employee not found'];
        $messages['employee_id.exists'] = ['Employee not found'];

        /* Add in events messages */
        $messages['event_id.exists'] = ['Event not found'];

       /* Add in Meeting Room messages */
        if($this->formTemplate->isMeetingRoom()) {

            $messages['form_template_id.required'] = ['Form template is required'];
            $messages['form_template_id.in'] = ['Form template is not a meeting room'];
            $messages['mrreservationtime.*.date.required'] = ['Date is required'];
            $messages['mrreservationtime.*.date.date'] = ['Date is invalid format'];
            $messages['mrreservationtime.*.start_time.required'] = ['Start time is required'];
            $messages['mrreservationtime.*.start_time.date_format'] = ['Start time is invalid format'];
            $messages['mrreservationtime.*.end_time.required'] = ['End time is required'];
            $messages['mrreservationtime.*.end_time.date_format'] = ['End time is invalid format'];

            // $messages['mr_title.required'] = ["Please input the title"];
            // $messages['mr_date.required'] = ["Please input the date"];
            // $messages['mr_date.date_format'] = ["Date does not match the required format (1990-01-01)"];
            // $messages['mr_start_time.required'] = ["Please input the start time"];
            // $messages['mr_start_time.date_format'] = ["Start time does not match the required format (12:00)"];
            // $messages['mr_end_time.required'] = ["Please input the end time"];
            // $messages['mr_end_time.date_format'] = ["End time does not match the required format (12:00)"];  
            // $messages['mr_end_time.after'] = ["End time must be greater than start time"];
        }          


        // dd($messages);
        return $messages;
    }
}
