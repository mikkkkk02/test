<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\FormTemplate;
use App\FormTemplateCategory;
use App\GovernmentForm;

class BenefitController extends Controller
{
    /**
     * Instantiate a new RequestController instance.
     *
     * @return void
     */
    public function __construct()
    {
        //     
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $templates = FormTemplate::select(FormTemplate::MINIMAL_COLUMNS)
                                ->where('form_template_category_id', '!=', FormTemplateCategory::FORM)
                                ->orderBy('name')
                                ->get();


        return view('pages.benefits.benefits', [
            'templates' => $templates,
        ]);	   
    } 

    /**
     * Fetch Internal and External Forms
     *
     * @return \Illuminate\Http\Response
     */
    public function fetchForms(Request $request)
    {       
        $internal = FormTemplate::select('id', 'name', 'description', 'sla_text', 'policy')
                                ->where('type', FormTemplate::HR)
                                ->get();
        $external = GovernmentForm::select('id', 'name')
                                ->get();

        $types = [
            [
                'label' => 'Internal',
                'templates' => $internal,
            ],
            [
                'label' => 'External',
                'templates' => $external,
            ]
        ];

        return response()->json([
            'types' => $types,
        ]);
    }
}
