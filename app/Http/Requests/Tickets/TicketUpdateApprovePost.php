<?php

namespace App\Http\Requests\Tickets;

use Illuminate\Foundation\Http\FormRequest;

class TicketUpdateApprovePost extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if(\Auth::check()) {
            $user = \Auth::user();


            /* Check if super admin */
            if(!$user->isSuperUser()) {

                $tmpTicketUpdate = TempTicketUpdate::findOrFail($request->id);
                
                if(!$tmpTicketUpdate->canApprove($user))
                    return false;
            }
        }

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {   
        return [
            //
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
            //
        ];
    }
}
