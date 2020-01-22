<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests\Forms\FormStorePost;
use App\Http\Requests\FormModal\FormStorePostStep1;

use App\Http\Requests\Forms\FormUpdateStatusPost;
use App\Http\Requests\Forms\FormAddUpdatePost;
use App\Http\Requests\Forms\FormRemoveUpdatePost;
use App\Http\Requests\Forms\FormAttachmentPost;
use App\Http\Requests\Forms\FormAttachmentRemovePost;

use App\Notifications\Requests\RequestWasUpdated;

use Carbon\Carbon;

use App\Form;
use App\TempForm;
use App\FormAttachment;
use App\FormUpdate;
use App\FormApprover;
use App\FormTemplate;
use App\FormTemplateCategory;
use App\FormTemplateField;
use App\FormTemplateOption;
use App\FormTemplateApprover;

use App\Event;
use App\Assignee;

use App\MrReservation;

class RequestController extends Controller
{
    /**
     * Instantiate a new RequestController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('App\Http\Middleware\Requests\CreateRequestMiddleware', ['only' => ['create']]);

        $this->middleware('App\Http\Middleware\Requests\ViewRequestMiddleware', ['only' => ['show', 'addAttachment', 'removeAttachment']]);

        $this->middleware('App\Http\Middleware\Requests\EditRequestUpdateMiddleware', ['only' => ['addUpdate', 'removeUpdate']]);

        $this->middleware('App\Http\Middleware\Requests\OwnerRequestMiddleware', ['only' => ['withdraw', 'archive', 'restore']]);
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


        return view('pages.requests.requests', [
            'templates' => $templates,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        $user = \Auth::user();
        $formTemplate = FormTemplate::select(FormTemplate::MINIMAL_COLUMNS)
                                    ->with('fields', 'fields.options')
                                    ->findOrFail($id);

        $assignees = Assignee::where('user_id', $user->id)
                                ->with('assigner')
                                ->get();


        return view('pages.requestmodal.createrequest', [
            'formTemplate' => $formTemplate,
            'assignees' => $assignees,
        ]);
    }

    public function createStep2($id)
    {
        $user = \Auth::user();
        $formTemplate = FormTemplate::select(FormTemplate::MINIMAL_COLUMNS)
                                    ->with('fields', 'fields.options')
                                    ->findOrFail($id);

        $assignees = Assignee::where('user_id', $user->id)
                                ->with('assigner')
                                ->get();


        return view('pages.requestmodal.createrequeststep2', [
            'formTemplate' => $formTemplate,
            'assignees' => $assignees,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function resubmit($id)
    {
        $form = Form::withTrashed()->select(Form::MINIMAL_COLUMNS)->findOrFail($id); 
        $formTemplate = FormTemplate::select(FormTemplate::MINIMAL_COLUMNS)->with('fields', 'fields.options')->findOrFail($form->template->id);

        $answers = $form->answers;
        $mrReservation = $form->mr_reservation;

        if ($mrReservation) {
            $mrReservation = $mrReservation->with('mr_reservation_times')->first();
        }

        $types = FormTemplateField::getTypes();
        $tableTypes = FormTemplateOption::getTypes();


        return view('pages.requests.createrequest', [
            'form' => $form,
            'formTemplate' => $formTemplate,
            'answers' => $answers,
            'types' => $types,
            'tableTypes' => $tableTypes,
            'mrReservation' => $mrReservation,
            'resubmit' => 1,        
        ]);
    }

    public function validateStep1(FormStorePostStep1 $request, $id) {
        return response()->json(true);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(FormStorePost $request, $id)
    {


        \DB::beginTransaction();

            $results = Form::createRequest($request, $id);

            if (isset($results['form'])) {
                if ($results['form']->template->isMeetingRoom()) {
                    MrReservation::submit($request, null, true, false, $results['form']);
                }
            }

        \DB::commit();
        
        return response()->json($results);
    }

    /**
     * Update a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(FormStorePost $request, $templateID, $formID)
    {
        $results = Form::createRequest($request, $templateID, $formID);
        
        return response()->json($results);
    }


    /**
     * Add an attachment to the resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function addAttachment(FormAttachmentPost $request, $id)
    {
        $form = Form::withTrashed()->findOrFail($id);
        $user = \Auth::user();


        /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();


        /* Store the image */
        if($request->file('attachment') && $request->file('attachment')->isValid()) {

            $name = $request->file('attachment')->getClientOriginalName();
            $attachment = $request->file('attachment')->store('form-attachments', 'public');

            /* Add attachment */
            $form->addAttachment($name, $attachment, $user);


            /* Create log */
            $form->createLog($user, 'Attached a file to the request', $form->renderViewURL());
        }


        /*
        | @End Transaction
        |---------------------------------------------*/
        \DB::commit();


        return response()->json([
            'response' => 1,
            'message' => 'Successfully added new attachment'
        ]);        
    }

    /**
     * Remove an attachment to the resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function removeAttachment(FormAttachmentRemovePost $request, $id)
    {
        $user = \Auth::user();
        $form = Form::withTrashed()->findOrFail($id);
        $attachment = FormAttachment::findOrFail($request->input('attachment_id'));


        /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();


        /* Store the image */
        if($attachment) {

            /* Delete file */
            \Storage::delete($attachment->path);

            /* Delete object */
            $attachment->delete();


            /* Create log */
            $form->createLog($user, 'Removed an attachment to the request', $form->renderViewURL());
        }


        /*
        | @End Transaction
        |---------------------------------------------*/
        \DB::commit();


        return response()->json([
            'response' => 1,
            'message' => 'Successfully removed attachment'
        ]); 
    }

    /**
     * Store an update to the resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function addUpdate(FormAddUpdatePost $request, $id)
    {
        $form = Form::withTrashed()->findOrFail($id);
        $user = \Auth::user();

        $vars = $request->all();
        $vars['form_id'] = $form->id;
        $vars['employee_id'] = $user->id;


        /* Create update */
        $formUpdate = FormUpdate::create($vars);

        /* Notify the employee */
        $form->employee->notify(new RequestWasUpdated($form, $formUpdate));

        /* Create log */
        $form->createLog($user, $request->description, $form->renderViewURL());

        return response()->json([
            'response' => 2,
            'message' => 'Added request update',
            'update' => $formUpdate,
        ]);
    }

    /**
     * Remove an update to the resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function removeUpdate(FormRemoveUpdatePost $request, $id)
    {
        $form = Form::findOrFail($id);
        $formUpdate = FormUpdate::findOrFail($request->input('form_update_id'));


        /* Delete update to the request */
        $formUpdate->delete();

        return response()->json([
            'response' => 3,          
            'id' => $request->input('form_update_id'),
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
        $form = Form::withTrashed()->findOrFail($id);
        $formTemplate = $form->template()->select(FormTemplate::MINIMAL_COLUMNS)->with('fields', 'fields.options')->first();

        $answers = $form->answers;
        $approvers = $form->approvers;
        $updates = $form->updates;
        $history = $form->history;

        $types = FormTemplateField::getTypes();
        $tableTypes = FormTemplateOption::getTypes();

        $mrReservation = $form->mr_reservation;


        return view('pages.requests.showrequest', [
            'form' => $form,
            'formTemplate' => $formTemplate,
            'answers' => $answers,
            'approvers' => $approvers,
            'updates' => $updates,
            'histories' => $history,
            'types' => $types,
            'tableTypes' => $tableTypes,
            'mrReservation' => $mrReservation,
        ]);
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
     * Approve the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function approve(FormUpdateStatusPost $request, $id)
    {
        $form = Form::findOrFail($id);
        $user = \Auth::user();

        $viewURL = route('request.show', $form->id);
        $reason = $request->has('reason') ? $request->input('reason') : '';


        /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();


        /* Approve the request */
        if($form->updateStatus(FormApprover::APPROVED, $reason)) {

            $message = 'Successfully approved request of ' . $form->employee->renderFullname() . ' for ' . $form->template->name;


            /* Create log */
            $form->createLog($user, 'Approved request', $form->renderViewURL());

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
                'redirectURL' => route('approvals'),
                'title' => 'Approve request',
                'message' => $message
            ]);
        }

        /* If auth fails */
        $message = 'Unauthorized request status update';
        /* Redirect to page on email approval */
        if(!$request->expectsJson()) {
            return view('pages.requests.emailapproval', [
                    'response' => 0,
                    'message' => $message,
                    'redirectURL' => $viewURL
                ]);
        }

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
        $form = Form::findOrFail($id);
        $user = \Auth::user();        

        $reason = $request->has('reason') ? $request->input('reason') : '';


        /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();


        /* Disapprove the request */
        if($form->updateStatus(FormApprover::DISAPPROVED, $reason)) {


            /* Create log */
            $form->createLog($user, $reason, $form->renderViewURL());


            /*
            | @End Transaction
            |---------------------------------------------*/
            \DB::commit();


            return response()->json([
                'response' => 1,
                'redirectURL' => route('approvals'),
                'title' => 'Disapprove request',                
                'message' => 'Successfully disapproved request of ' . $form->employee->renderFullname() . ' for ' . $form->template->name
            ]);
        }


        return response(['Permission' => 'Unauthorized request status update'], 422);
    }  

    /**
     * Withdraw the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function withdraw(FormUpdateStatusPost $request, $id)
    {
        $form = Form::findOrFail($id);
        $user = \Auth::user();


        /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();


        /* Withdraw the request */
        $form->withdraw();

        /* Create log */
        $form->createLog($user, 'Withdraw request', $form->renderViewURL());


        /*
        | @End Transaction
        |---------------------------------------------*/
        \DB::commit();


        return response()->json([
            'response' => 1,
            'redirectURL' => route('requests'),
            'title' => 'Withdraw request',
            'message' => 'Successfully withdrawn request for ' . $form->template->name
        ]);


    }      

    /**
     * Archive the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function withdrawAll(FormUpdateStatusPost $request, $id)
    {
        $requestId = $request->id;

        foreach ($requestId as $ids ) {

            $form = Form::findOrFail($ids);
        
            $user = \Auth::user();

            /*
            | @Begin Transaction
            |---------------------------------------------*/
            \DB::beginTransaction();
            
            $form->withdraw();

            /* Create log */
            $form->createLog($user, 'Withdraw request', $form->renderViewURL());

            /*
            | @End Transaction
            |---------------------------------------------*/
            \DB::commit();
        }

        return response()->json([
                'response' => 1,
                'redirectURL' => route('requests'),
                'title' => 'Withdraw request',
                'message' => 'Successfully withdrawn request for ' . $form->template->name
            ]);
 
    } 
    /**
     * Archive the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function archive($id)
    {
        $form = Form::findOrFail($id);


        /* Soft delete group */
        $form->delete();

        return response()->json([
            'response' => 1,
            'redirectURL' => route('request.show', $form->id),
            'title' => 'Archive Form',
            'message' => 'Successfully archived ' . $form->template->name
        ]);
    }

    /**
     * Restore the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        $form = Form::onlyTrashed()->findOrFail($id);


        /* Restore group */
        $form->restore();

        return response()->json([
            'response' => 1,
            'redirectURL' => route('request.show', $form->id),
            'title' => 'Restore Form',
            'message' => 'Successfully restored ' . $form->template->name
        ]);
    } 

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
