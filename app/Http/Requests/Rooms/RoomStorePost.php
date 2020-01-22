<?php

namespace App\Http\Requests\Rooms;

use Illuminate\Foundation\Http\FormRequest;

class RoomStorePost extends FormRequest
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
            'name' => 'required',
            'location_id' => 'required|exists:locations,id',
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
            'name.required' => 'Please enter the room name',
            'location_id.required' => 'Please select a location',
            'location_id.exists' => 'Location not found',
        ];
    }
}
