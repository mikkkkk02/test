<?php

namespace App\Http\Requests\Learnings;

use Illuminate\Foundation\Http\FormRequest;

use Carbon\Carbon;

class LearningStorePost extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return \Auth::check() && \Auth::user()->hasRole('Administrator');
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
            'learning_category_id' => 'required|exists:learning_categories,id',
            'company_id' => 'required|exists:companies,id',

            'title' => 'required',
            'venue' => 'required',
            'provider' => 'required',

            'start_date' => 'date_format:"Y-m-d"',
            'end_date' => 'after:start_date|date_format:"Y-m-d"',

            'times' => 'required|array|size:' . $limit,
            'times.*.start_time' => 'required|date_format:"G:i:s"',
            'times.*.end_time' => 'required|date_format:"G:i:s"',            

            'type' => 'required|min:0|max:2',
            'type_others' => 'required_if:type,2',

            'objective' => 'required',
            'application' => 'required',

            'cost_center' => 'required',

            'course_fee' => 'required|numeric',
            'accomodation_fee' => 'required|numeric',
            'meal_fee' => 'required|numeric',
            'transport_fee' => 'required|numeric',
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
            'learning_category_id.required' => 'Category not found',
            'learning_category_id.exists' => 'Category not found',

            'company_id.required' => 'Company not found',
            'company_id.exists' => 'Company not found',

            'title.required' => 'Please input the title',

            'venue.required' => 'Please input the venue',
            'provider.required' => 'Please input the provider',

            'start_date.date_format' => "The start date does not match the required format (1990-01-01)",

            'end_date.after_or_equal' => "The end date must be less than or equal to the start date",
            'end_date.date_format' => "The end date does not match the required format (1990-01-01)",

            'times.required' => 'Please input the start/end time',
            'times.array' => 'Please input the start/end time',
            'times.size' => 'Please input the start/end time',
            'times.*.start_time.required' => 'Please input the start/end time',
            'times.*.end_time.required' => 'Please input the start/end time',
            'times.*.start_time.date_format' => 'The start time does not match the required format (24:00:00)',
            'times.*.end_time.date_format' => 'The end time does not match the required format (24:00:00)',

            'type.required' => 'Please select the type',
            'type.min' => 'Invalid type',
            'type.max' => 'Invalid type',
            'type_others.required' => 'Please input the type',

            'objective.required' => 'Please input the objective',
            'application.required' => 'Please input the application',

            'cost_center.required' => 'Please input the cost center',

            'course_fee.required' => 'Please input the course fee',
            'course_fee.numeric' => 'Invalid course fee',

            'accomodation_fee.required' => 'Please input the accomodation fee',
            'accomodation_fee.numeric' => 'Invalid accomodation fee',

            'meal_fee.required' => 'Please input the meal fee',
            'meal_fee.numeric' => 'Invalid meal fee', 

            'transport_fee.required' => 'Please input the transport fee',
            'transport_fee.numeric' => 'Invalid transport fee',
        ];
    }      
}
