<?php

namespace App\Http\Requests\Tickets;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Factory as ValidationFactory;

use Route;
use App\Form;

class TicketUpdateTravelOrder extends FormRequest
{
    protected $formTemplate;
    protected $fields;

    public function __construct(ValidationFactory $validationFactory) {

        $this->formTemplate = Form::withTrashed()->find(Route::current()->parameter('formID'))->template;
        $this->fields = $this->formTemplate->fetchAvailableTablefields(true);
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
        return $this->formTemplate->getValidationRules($this->fields);
    }


    /**
     * Get the error messages.
     *
     * @return array
     */
    public function messages()
    {
        return $this->formTemplate->getValidationMessages($this->fields);
    }
}
