<?php

namespace App\Http\Requests\Events;

use Illuminate\Http\Request;
use Illuminate\Foundation\Http\FormRequest;

use Carbon\Carbon;

class EventStorePost extends FormRequest
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
    public function rules(Request $request)
    {
        /* Fetch inputted days */
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        /* Convert days to Carbon for easy calculation */
        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate);

        /* Calculate no. of days */
        $limit = $endDate->diffInDays($startDate);


        return [
            'form_template_id' => 'required|exists:form_templates,id',
            'event_category_id' => 'nullable|exists:event_categories,id',

            'title' => 'required',
            'limit' => 'nullable|numeric|min:0',
            'hours' => 'nullable|numeric|min:0',
            'color' => 'size:6',
            'description' => 'required',

            'start_date' => 'date_format:"Y-m-d"',
            'end_date' => 'after_or_equal:start_date|date_format:"Y-m-d"',

            'times' => 'required|array|min:1|max:' . ($limit + 1),
            'times.*.start_time' => 'required|date_format:"g:i A"',
            'times.*.end_time' => 'required|after:times.*.start_time|date_format:"g:i A"',
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
            'form_template_id.required' => 'Form template not found',
            'form_template_id.exists' => 'Form template not found',

            'event_category_id.exists' => 'Event category not found',            
            
            'title.required' => 'Please input the form title',

            'limit.numeric' => 'Limit must be a number',
            'limit.min' => 'Limit cannot be a negative value',

            'color.size' => 'Invalid color',

            'hours.numeric' => 'No of. Hours must be a number',
            'hours.min' => 'Hours cannot be a negative value',

            'description' => 'Please input the description',

            'start_date.date_format' => "The start date does not match the required format (1990-01-01)",

            'end_date.after_or_equal' => "The end date must be less than or equal to the start date",
            'end_date.date_format' => "The end date does not match the required format (1990-01-01)",

            'times.required' => 'Please input the start/end time',
            'times.array' => 'Please input the start/end time',
            'times.size' => 'Please input the start/end time',
            'times.min' => 'There seems to be a problem w/ the time settings',
            'times.max' => 'There seems to be a problem w/ the time settings',
            'times.*.start_time.required' => 'Please input the start/end time',
            'times.*.start_time.date_format' => 'The start time does not match the required format (12:00 AM/PM)',
            'times.*.end_time.required' => 'Please input the start/end time',
            'times.*.end_time.after' => 'The end date must be greater than the start date',
            'times.*.end_time.date_format' => 'The end time does not match the required format (12:00 AM/PM)',
        ];
    }
}
