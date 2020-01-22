<?php

namespace App\Http\Requests\Forms;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Factory as ValidationFactory;
use Illuminate\Validation\Rule;

use App\Http\Requests\MrReservations\MrReservationStorePost;

use Route;
use Carbon\Carbon;
use App\Form;
use App\FormTemplate;

class FormStorePost extends FormRequest
{
    protected $formTemplate;


    public function __construct(ValidationFactory $validationFactory) {

        $route = Route::current()->getName();

        /* Check route type (Store/Update) */
        if($route == 'request.store') {

            $this->formTemplate = FormTemplate::find(Route::current()->parameter('id'));

        } else if($route == 'request.edit') {

            $this->formTemplate = Form::withTrashed()->find(Route::current()->parameter('formID'))->template;

        }
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
        $route = Route::current()->getName();

        if($route == 'mrreservation.store' || $route == 'mrreservation.edit') { 

            $this->formTemplate = FormTemplate::withTrashed()->find($this->input('form_template_id'));

        }
        
        /* Include required on publish only */
        if($this->has('save'))
            $rules = $this->formTemplate->getValidationRules();

        /* Add in employee rules */
        $rules['employee_id'] = 'required|exists:users,id';


        /* Add in event rules */
        $rules['event_id'] = 'nullable|exists:events,id';

        /* Add in L&D rules */
        if($this->formTemplate->category->forLearning()) {

            $rules['course_cost'] = 'required|numeric';
            $rules['accommodation_cost'] = 'required|numeric';
            $rules['meal_cost'] = 'required|numeric';
            $rules['transport_cost'] = 'required|numeric';
        }

        /* Add in Meeting Room rules */
        if($this->formTemplate->isMeetingRoom()) {

            $ids = FormTemplate::where('request_type', FormTemplate::MEETINGROOM)->pluck('id')->toArray();

            // $rules['mr_title'] = 'required';
            // $rules['mr_date'] = 'required|date_format:"Y-m-d"|meeting_room:mr_start_time,mr_end_time';
            // $rules['mr_start_time'] = 'required|date_format:"H:i"';
            // $rules['mr_end_time'] = 'required|date_format:"H:i"|after:mr_start_time';

            $rules['name'] = 'required';
            // $rules['color'] = 'required';
            $rules['location_id'] = 'required';
            // $rules['room_id'] = 'required';

            $rules['form_template_id'] = 'required|' . Rule::in($ids);
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

        $messages['purpose.required'] = ['Please input the purpose'];


        /* Add in events messages */
        $messages['event_id.exists'] = ['Event not found'];


        /* Add in L&D messages */
        if($this->formTemplate->category->forLearning()) {

            $messages['course_cost.required'] = ["Course Cost not found"];
            $messages['course_cost.numeric'] = ["Course cost must be numeric"];
            $messages['accommodation_cost.required'] = ["Accommodation Cost not found"];
            $messages['accommodation_cost.numeric'] = ["Accommodation cost must be numeric"];
            $messages['meal_cost.required'] = ["Meal Cost not found"];
            $messages['meal_cost.numeric'] = ["Meal cost must be numeric"];
            $messages['transport_cost.required'] = ["Transport Cost not found"];
            $messages['transport_cost.numeric'] = ["Transport cost must be numeric"];
        }

       /* Add in Meeting Room messages */
        if($this->formTemplate->isMeetingRoom()) {

            $messages['name.required'] = ['Name is required'];
            $messages['color.required'] = ['Color is required'];
            $messages['location_id.required'] = ['Please select a location'];
            $messages['room_id.required'] = ['Please select a room'];


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
