<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\FormTemplate;
use App\FormTemplateCategory;

class LearningNookController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $ldForm = FormTemplate::where('form_template_category_id', FormTemplateCategory::LD)->get()->first();
        $ldCreateURL = $ldForm ? route('request.create', $ldForm->id) : null;

        return view('pages.learningnook.learningnook', [
            'ldCreateURL' => $ldCreateURL,
            'templateId' => $ldForm->id,
        ]);
    }
}
