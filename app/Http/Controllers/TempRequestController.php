<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests\Forms\FormUpdateStatusPost;

use Carbon\Carbon;

use App\Form;
use App\TempForm;
use App\FormAttachment;
use App\FormUpdate;
use App\FormApprover;
use App\TempFormApprover;
use App\FormTemplate;
use App\FormTemplateCategory;
use App\FormTemplateField;
use App\FormTemplateOption;
use App\FormTemplateApprover;

use App\Event;
use App\Assignee;

class TempRequestController extends Controller
{
    /**
     * Instantiate a new RequestController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('App\Http\Middleware\FormUpdates\ViewFormUpdateMiddleware', ['only' => ['show']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $templates = FormTemplate::select(FormTemplate::MINIMAL_COLUMNS)
                                ->where('form_template_category_id', '!=', FormTemplateCategory::EVENT)
                                ->orderBy('name')
                                ->get();


        return view('pages.formupdates.formupdates', [
            'templates' => $templates,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $tempForm = TempForm::withTrashed()->findOrFail($id);
        $form = $tempForm->form;
        $formTemplate = FormTemplate::select(FormTemplate::MINIMAL_COLUMNS)->with('fields', 'fields.options')->findOrFail($form->template->id);

        $newAnswers = $tempForm->answers;
        $oldAnswers = $tempForm->isApproved() ? $tempForm->history->answers : $form->answers;
        $approvers = $tempForm->approvers;

        $types = FormTemplateField::getTypes();
        $tableTypes = FormTemplateOption::getTypes();

        $noBoxInfo = true;

        return view('pages.formupdates.showformupdate', [
            'tempForm' => $tempForm,
            'form' => $form,
            'formTemplate' => $formTemplate,
            'newAnswers' => $newAnswers,
            'oldAnswers' => $oldAnswers,
            'approvers' => $approvers,
            'types' => $types,
            'tableTypes' => $tableTypes,            
            'noBoxInfo' => $noBoxInfo,
        ]);
    }


    /**
     * Approve the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function approve(FormUpdateStatusPost $request, $id)
    {
        $tempForm = TempForm::findOrFail($id);
        $user = \Auth::user();

        $viewURL = route('temprequest.show', $tempForm->id);
        $reason = $request->has('reason') ? $request->input('reason') : '';


        /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();


        /* Approve the request */
        if($tempForm->updateStatus(FormApprover::APPROVED, $reason)) {

            $message = 'Successfully approved request update of ' . $tempForm->employee->renderFullname() . ' for ' . $tempForm->template->name;


            /*
            | @End Transaction
            |---------------------------------------------*/
            \DB::commit();


            /* Redirect to page on email approval */
            if(!$request->expectsJson())
                return view('pages.requests.emailapproval', [
                        'response' => 1,
                        'message' => $message,
                        'redirectURL' => $viewURL
                    ]);

            return response()->json([
                'response' => 1,
                'redirectURL' => route('temprequests'),
                'title' => 'Approve request',
                'message' => $message
            ]);
        }

        /* If auth fails */
        $message = 'Unauthorized request status update';
        /* Redirect to page on email approval */
        if(!$request->expectsJson())
            return view('pages.requests.emailapproval', [
                    'response' => 0,
                    'message' => $message,
                    'redirectURL' => $viewURL
                ]);

        return response(['Permission' => $message], 422);
    }

    /**
     * Disapprove the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function disapprove(FormUpdateStatusPost $request, $id)
    {
        $tempForm = TempForm::findOrFail($id);
        $user = \Auth::user();        

        $reason = $request->has('reason') ? $request->input('reason') : '';


        /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();


        /* Disapprove the request */
        if($tempForm->updateStatus(FormApprover::DISAPPROVED, $reason)) {


            /*
            | @End Transaction
            |---------------------------------------------*/
            \DB::commit();


            return response()->json([
                'response' => 1,
                'redirectURL' => route('temprequests'),
                'title' => 'Disapprove request',                
                'message' => 'Successfully disapproved request update of ' . $tempForm->employee->renderFullname() . ' for ' . $tempForm->template->name
            ]);
        }


        return response(['Permission' => 'Unauthorized request status update'], 422);
    }  

}
