<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests\FormTemplates\FormTemplateStorePost;
use App\Http\Requests\FormTemplates\FormTemplateUpdatePost;
use App\Http\Requests\FormTemplates\FormTemplateUpdateApproverPost;
use App\Http\Requests\FormTemplates\FormTemplateAddContactPost;
use App\Http\Requests\FormTemplates\FormTemplateRemoveContactPost;
use App\Http\Requests\FormTemplates\FormTemplateAddApproverPost;
use App\Http\Requests\FormTemplates\FormTemplateRemoveApproverPost;
use App\Http\Requests\FormTemplates\FormTemplateAddFieldPost;
use App\Http\Requests\FormTemplates\FormTemplateRemoveFieldPost;
use App\Http\Requests\FormTemplates\FormTemplateUpdateSortingPost;

use App\User;
use App\FormTemplate;
use App\FormTemplateCategory;
use App\FormTemplateContact;
use App\FormTemplateField;
use App\FormTemplateApprover;
use App\FormApprover;
use App\Form;

class FormTemplateController extends Controller
{
    /**
     * Instantiate a new CompanyController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('App\Http\Middleware\FormTemplates\FormTemplateIndexMiddleware', ['only' => ['index', 'create']]);

        $this->middleware('App\Http\Middleware\FormTemplates\ViewFormTemplateMiddleware', ['only' => ['show', 'update', 'addContact', 'removeContact', 'addField', 'removeField', 'addApprover', 'removeApprover', 'updateApprover', 'updateSorting', 'archive', 'restore']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('pages.formtemplates.formtemplates');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $formCategories = FormTemplateCategory::select(FormTemplateCategory::MINIMAL_COLUMNS)->get();
        $priorities = FormTemplate::getPriorities();
        $formTemplateTypes = FormTemplate::getType();
        $requestTypes = FormTemplate::getRequestType();


        return view('pages.formtemplates.createformtemplate', [
            'formCategories' => $formCategories,
            'priorities' => $priorities,
            'formTemplateTypes' => $formTemplateTypes,
            'requestTypes' => $requestTypes,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(FormTemplateStorePost $request)
    {
        $vars = $request->all();
        $vars['creator_id'] = \Auth::user()->id;
        $vars['updater_id'] = \Auth::user()->id;


        /* Create the form template */
        $formTemp = FormTemplate::create($vars);

        return response()->json([
            'response' => 1,
            'redirectURL' => route('formtemplate.show', $formTemp->id),
            'title' => 'Create form template',
            'message' => 'Successfully created ' . $formTemp->name
        ]);
    }

    /**
     * Store a new contact for the resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function addContact(FormTemplateAddContactPost $request, $id)
    {
        $formTemp = FormTemplate::withTrashed()->findOrFail($id);
        $contact = FormTemplateContact::create($request->all());


        /* Update last editor */
        $formTemp->updater_id = \Auth::user()->id;
        $formTemp->save();

        /* Add a form template contact */
        $formTemp->addContact($contact);


        return response()->json([
            'response' => 1,
            'data' => $contact,
        ]);
    }

    /**
     * Remove resource's contact in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function removeContact(FormTemplateRemoveContactPost $request, $id)
    {
        $formTemp = FormTemplate::withTrashed()->findOrFail($id);        
        $contact = FormTemplateContact::findOrFail($request->input('id'));


        /* Update last editor */
        $formTemp->updater_id = \Auth::user()->id;
        $formTemp->save();

        /* Delete form template contact */
        $contact->delete();

        return response()->json([
            'response' => 2,
            'data' => $request->input('id'),
        ]);
    }

    /**
     * Store a new field for the resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function addField(FormTemplateAddFieldPost $request, $id)
    {
        $formTemp = FormTemplate::withTrashed()->findOrFail($id);


        /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();


        /* Update last editor */
        $formTemp->updater_id = \Auth::user()->id;
        $formTemp->save();

        /* Add a form template field */
        $field = $formTemp->addField($request);


        /*
        | @End Transaction
        |---------------------------------------------*/
        \DB::commit();


        return response()->json([
            'response' => 1,
            'message' => 'Added ' . $field->renderType(),
            'field' => $field,
        ]);
    }

    /**
     * Remove resource's field in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function removeField(FormTemplateRemoveFieldPost $request, $id)
    {
        $formTemp = FormTemplate::withTrashed()->findOrFail($id);        
        $field = FormTemplateField::findOrFail($request->input('form_template_field_id'));


        /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();


        /* Update last editor */
        $formTemp->updater_id = \Auth::user()->id;
        $formTemp->save();

        /* Delete form template field */
        $formTemp->removeField($field);


        /*
        | @End Transaction
        |---------------------------------------------*/
        \DB::commit();


        return response()->json([
            'response' => 1,
        ]);
    }   

    /**
     * Store a new approver for the resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function addApprover(FormTemplateAddApproverPost $request, $id)
    {
        $formTemp = FormTemplate::withTrashed()->findOrFail($id);
        $approver = FormTemplateApprover::create($request->all());


        /* Update last editor */
        $formTemp->updater_id = \Auth::user()->id;
        $formTemp->save();

        /* Add a form template approver */
        $formTemp->addApprover($approver);


        return response()->json([
            'response' => 1,
            'data' => $approver,
        ]);
    }

    /**
     * Remove resource's approver in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function removeApprover(FormTemplateRemoveApproverPost $request, $id)
    {
        $formTemp = FormTemplate::withTrashed()->findOrFail($id);
        $approver = FormTemplateApprover::findOrFail($request->input('id'));


        /* Update last editor */
        $formTemp->updater_id = \Auth::user()->id;
        $formTemp->save();

        

        if($formTemp->isInOrder()){

            ##All approver of this form template
            $formTempApprovers = FormTemplateApprover::where('form_template_id',$formTemp->id)->get();


            ##Get all list of formTemplateApprovers
            $formTempApproversId = array();
            foreach($formTempApprovers as $formTempApprover){

                $formTempApproversId[] =  $formTempApprover->id;
            }


            ##Select FormApprover 
            $form_approver = FormApprover::where('form_template_approver_id',$approver->id)->get();

            for ($i=0; $i < count($form_approver); $i++) { 
                
                ##Search FormApprover 'form_template_approver_id' column in $formTempApproversId array
                $key = array_search($form_approver[$i]['form_template_approver_id'], $formTempApproversId);
                
                ##If enabled column is equal to 1
                if($form_approver[$i]['enabled'] == 1){

                    ##$formTempApproversId keys 
                    if(isset($formTempApproversId[$key+1] )){

                        ##Get the next approver
                        $next = FormApprover::where([
                            ['form_id', $form_approver[$i]['form_id']],
                            ['form_template_approver_id', $formTempApproversId[$key+1]]
                         ])->get();


                        if(count($next) > 0){
                           
                            $next[0]->enabled = 1;
                            $next[0]->update();
                        }
                    }
                }
                ##If status of from approver is 0 update to 3
                if($form_approver[$i]['status'] == FormApprover::PENDING ){

                    $form_approver[$i]['status'] = FormApprover::STOP;
                   
                }

                ## Update enabled to 0
                $form_approver[$i]['enabled'] = 0;
                // $form_approver[$i]['approved_date'] = date('Y-m-d H:i:s');
                $form_approver[$i]->update();       
            }

        }

        /* Delete form template approver */
        $approver->delete();

          
        $formApprovers = FormApprover::where([
            ['type_value', '=', $approver->type_value ],
            ['status', '=', FormApprover::PENDING],
            ['form_template_approver_id', '=', $approver->id]
        ])->get();
        

        foreach($formApprovers as $approverStatus){

            $approverStatus->status = FormApprover::STOP;
            $approverStatus->enabled = 0;
            $approverStatus->update();
            // $approverStatus->delete();
        }
     
        
        return response()->json([
            'response' => 2,
            'data' => $request->input('id'),
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
        $formTemplate = FormTemplate::withTrashed()->findOrFail($id);
        $formCategories = FormTemplateCategory::select(FormTemplateCategory::MINIMAL_COLUMNS)->get();
        $priorities = FormTemplate::getPriorities();
        $formTemplateTypes = FormTemplate::getType();
        $requestTypes = FormTemplate::getRequestType();

        $types = FormTemplateField::getTypes();

        $employees = User::select(User::MINIMAL_COLUMNS)->orderBy('last_name', 'asc')->get();
        
        return view('pages.formtemplates.showformtemplate', [
            'formTemplate' => $formTemplate,
            'formCategories' => $formCategories,
            'priorities' => $priorities,
            'formTemplateTypes' => $formTemplateTypes,
            'requestTypes' => $requestTypes,
            
            'types' => $types,
            'employees' => $employees,
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(FormTemplateUpdatePost $request, $id)
    {
        $formTemp = FormTemplate::withTrashed()->findOrFail($id);
        $vars = $request->except(['enableAttachment', 'enableManagerial']);
        $vars['updater_id'] = \Auth::user()->id;
        $vars['enableAttachment'] = $request->has('enableAttachment');
        $vars['enableManagerial'] = $request->has('enableManagerial');


        /* Update the form template */
        $formTemp->update($vars);

        /* Add in special message for the diff. types */
        $message = 'Successfully updated ' . $formTemp->name . '<br><br>';
        switch($formTemp->request_type) {
            case FormTemplate::MEETINGROOM:

                $message .= '<h4>Meeting Room - Special Message!</h4>';
                $message .= '<p>All meeting room type template will automatically have the title, date, start and end time.</p>';

            break;            
        }

        /* Add in special message for the diff. categories */
        switch($formTemp->form_template_category_id) {
            case FormTemplateCategory::LD:

                $message .= '<h4>L&D Special Message!</h4>';
                $message .= '<p>All learning & development category template will automatically have the course, accommodation, meal, transport, others and total cost.</p>';

            break;            
        }        


        return response()->json([
            'response' => 1,
            'title' => 'Update form template details',
            'message' => $message
        ]);
    }


    /**
     * Update the specified resource's approver settings in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateApprover(FormTemplateUpdateApproverPost $request, $id)
    {
        $formTemp = FormTemplate::withTrashed()->findOrFail($id);
        $vars = $request->all();


        /* Update last editor */
        $formTemp->updater_id = \Auth::user()->id;
        $formTemp->save();

        /* Update the form template */
        $formTemp->update($vars);


        return response()->json([
            'response' => 1,
            'title' => 'Update form template approver settings',
            'message' => 'Successfully updated ' . $formTemp->name
        ]);
    } 

    /**
     * Update sorting of the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateSorting(FormTemplateUpdateSortingPost $request, $id)
    {
        $formTemp = FormTemplate::findOrFail($id);


        /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();


        /* Update last editor */
        $formTemp->updater_id = \Auth::user()->id;
        $formTemp->save();

        /* Update the form template field sort */
        $formTemp->updateSorting($request->input('sorting'));


        /*
        | @End Transaction
        |---------------------------------------------*/
        \DB::commit();


        return response()->json([
            'response' => 1,
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
        $formTemplate = FormTemplate::findOrFail($id);


        /* Soft delete form template */
        $formTemplate->delete();

        return response()->json([
            'response' => 1,
            'redirectURL' => route('formtemplate.show', $formTemplate->id),
            'title' => 'Archive form template',
            'message' => 'Successfully archived ' . $formTemplate->name
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
        $formTemplate = FormTemplate::onlyTrashed()->findOrFail($id);


        /* Restore form template */
        $formTemplate->restore();

        return response()->json([
            'response' => 1,
            'redirectURL' => route('formtemplate.show', $formTemplate->id),
            'title' => 'Restore form template',
            'message' => 'Successfully restored ' . $formTemplate->name
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
