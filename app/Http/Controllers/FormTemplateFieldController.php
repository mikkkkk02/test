<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests\FormTemplates\FormTemplateAddFieldPost;
use App\Http\Requests\FormTemplates\FormTemplateUpdateFieldPost;
use App\Http\Requests\FormTemplates\FormTemplateRemoveFieldPost;
use App\Http\Requests\FormTemplates\FormTemplateAddOptionPost;
use App\Http\Requests\FormTemplates\FormTemplateRemoveOptionPost;

use App\FormTemplateOption;
use App\FormTemplateField;

class FormTemplateFieldController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Update the specified resource's option in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function addOption(FormTemplateAddOptionPost $request, $id)
    {
        $field = FormTemplateField::findOrFail($id);


        /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();


        /* Update last editor */
        $field->form_template->updater_id = \Auth::user()->id;
        $field->form_template->save();

        /* Add the option */
        $option = $field->addOption($request);


        /*
        | @End Transaction
        |---------------------------------------------*/
        \DB::commit();


        return response()->json([
            'response' => 5,
            'option' => $option,
            'message' => "Added '" . $option->value . "' option",
        ]);
    }

    /**
     * Remove resource's option in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function removeOption(FormTemplateRemoveOptionPost $request, $id)
    {
        $field = FormTemplateField::findOrFail($id);        
        $option = FormTemplateOption::findOrFail($request->input('form_template_option_id'));
        $optionTmp = $option;

        if($option->form_template_field->isTravelOrderDetailTable()) {
            return response()->json([
                'response' => 4,
                'status' => 0,
                'message' => "Field cannot be deleted as its being used as the Travel Order Details",
            ]);
        }


        /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();


        /* Update last editor */
        $field->form_template->updater_id = \Auth::user()->id;
        $field->form_template->save();

        /* Delete form template option */
        $field->removeOption($option);

        /*
        | @End Transaction
        |---------------------------------------------*/
        \DB::commit();


        return response()->json([
            'response' => 6,
            'option' => $optionTmp->id,
            'message' => "Removed '" . $optionTmp->value . "' option",
        ]);
    }    

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(FormTemplateUpdateFieldPost $request, $id)
    {
        $field = FormTemplateField::findOrFail($id);
        $vars = $request->except(['id', 'extra', 'options']);


        /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();


        /* Update last editor */
        $field->form_template->updater_id = \Auth::user()->id;
        $field->form_template->save();

        /* Update the form template field */
        $result1 = $field->updateField($vars);
        /* Update options */
        $result2 = $field->updateOptions($request->input('options'));


        /* Check if updating of options doesn't have a problem */
        if($result1 && $result2) {

            /*
            | @End Transaction
            |---------------------------------------------*/
            \DB::commit();


            return response()->json([
                'response' => 3,
                'status' => 1,
                'message' => 'Update successful!',
            ]);

        } else {

            return response()->json([
                'response' => 3,
                'status' => 0,
                'message' => "Option field must be required or a datefield as its being used as the starting date for the form's SLA",
            ]);
        }
    }   

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(FormTemplateRemoveFieldPost $request, $id)
    {
        $field = FormTemplateField::findOrFail($id);
        $formTemp = $field->form_template;


        /* Chech if current field is the SLA start date */
        $isSLA = $field->isSLADate();

        /* Check if SLA settings is on this column */
        if($isSLA) {

            return response()->json([
                'response' => 4,
                'status' => 0,
                'message' => "Field cannot be deleted as its being used as the starting date for the form's SLA",
            ]); 

        } else if($field->isTravelOrderDetailTable()) {
            return response()->json([
                'response' => 4,
                'status' => 0,
                'message' => "Field cannot be deleted as its being used as the Travel Order Details",
            ]);
        } else {

            /*
            | @Begin Transaction
            |---------------------------------------------*/
            \DB::beginTransaction();


            /* Update last editor */
            $field->form_template->updater_id = \Auth::user()->id;
            $field->form_template->save();
            
            /* Delete field */
            $field->options()->delete();
            $field->delete();

            /* Update sorting */
            $formTemp->refreshFieldSorting();


            /*
            | @End Transaction
            |---------------------------------------------*/
            \DB::commit();


            return response()->json([
                'response' => 4,
                'status' => 1,
                'id' => $id,
            ]); 
        }       
    }
}
