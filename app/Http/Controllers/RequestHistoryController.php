<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Form;
use App\FormHistory;
use App\FormTemplate;
use App\FormTemplateField;
use App\FormTemplateOption;
use App\FormHistoryMrReservation;

class RequestHistoryController extends Controller
{
    /**
     * Instantiate a new RequestController instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('App\Http\Middleware\Requests\ViewRequestMiddleware', ['only' => ['show', 'addAttachment', 'removeAttachment']]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $formHistory = FormHistory::findOrFail($id);
        $formTemplate = $formHistory->form->template()->select(FormTemplate::MINIMAL_COLUMNS)->with('fields', 'fields.options')->first();

        $answers = $formHistory->answers;

        $types = FormTemplateField::getTypes();
        $tableTypes = FormTemplateOption::getTypes();

        $mrReservation = $formHistory->mr_reservation;

        if ($mrReservation) {
            $mrReservation = FormHistoryMrReservation::with('mr_reservation_times')->where('id', $mrReservation->id)->first();
        }

        return view('pages.formhistory.showformhistory', [
            'formHistory' => $formHistory,
            'formTemplate' => $formTemplate,
            'answers' => $answers,
            'types' => $types,
            'tableTypes' => $tableTypes,
            'mrReservation' => $mrReservation,
        ]);
    }    
}
