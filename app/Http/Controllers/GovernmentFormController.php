<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests\GovernmentForms\GovernmentFormStorePost;
use App\Http\Requests\GovernmentForms\GovernmentFormAttachmentPost;

use App\Helper\RouteChecker;

use App\GovernmentForm;

class GovernmentFormController extends Controller
{
    /**
     * Instantiate a new GovernmentFormController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('App\Http\Middleware\GovernmentForms\EditGovernmentFormMiddleware', ['only' => ['store', 'update', 'addAttachment', 'archive', 'restore']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('pages.governmentforms.governmentforms');
    }    

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.governmentforms.creategovernmentform', [
            'canEdit' => 1,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(GovernmentFormStorePost $request)
    {
        $user = \Auth::user();

        $vars = $request->all();
        $vars['creator_id'] = $user->id;
        $vars['updater_id'] = $user->id;


        /* Create the government form */
        $governmentForm = GovernmentForm::create($vars);

        return response()->json([
            'response' => 1,
            'redirectURL' => route('governmentform.show', $governmentForm->id),
            'title' => 'Create Government Form',
            'message' => 'Successfully created government form ' . $governmentForm->name
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    { 
        $routeChecker = new RouteChecker(\Request::route());
        $governmentForm = GovernmentForm::withTrashed()->findOrFail($id);

        /* Can Permission */
        $canEdit = $routeChecker->hasModuleRoles(['Adding/Editing of Government Forms']);


        return view('pages.governmentforms.showgovernmentform', [
            'governmentForm' => $governmentForm,

            'canEdit' => $canEdit,
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
    public function update(GovernmentFormStorePost $request, $id)
    {
        $governmentForm = GovernmentForm::withTrashed()->findOrFail($id);
        
        $vars = $request->all();
        $vars['updater_id'] = \Auth::user()->id;


        /* Update the government form */
        $governmentForm->update($vars);

        return response()->json([
            'response' => 1,
            'title' => 'Update government form details',
            'message' => 'Successfully updated government form ' . $governmentForm->name
        ]);    
    }

    /**
     * Add an attachment to the resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function addAttachment(GovernmentFormAttachmentPost $request, $id)
    {
        $governmentForm = GovernmentForm::findOrFail($id);


        /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();



        /* Store the image */
        if($request->file('attachment')) {

            /* Create validator */
            $validator = \Validator::make(
                [
                    'file'      => $request->file('attachment'),
                    'extension' => strtolower($request->file('attachment')->getClientOriginalExtension()),
                ],
                [
                    'file'          => 'required',
                    'extension'      => 'required|in:pdf,doc,xlsx,xls,docx,odt,ods,odp',
                ]
            );

            /* Check file type */
            if($validator->fails())
                return response(['File Type' => 'The attachment must be a pdf or excel file'], 422);


            $name = $request->file('attachment')->getClientOriginalName();
            $attachment = $request->file('attachment')->store('government-forms', 'public');

            /* Add attachment */
            $governmentForm->addAttachment($name, $attachment);
        }


        /*
        | @End Transaction
        |---------------------------------------------*/
        \DB::commit();


        return response()->json([
            'response' => 1,
            'message' => 'Successfully uploaded file'
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
        $governmentform = GovernmentForm::findOrFail($id);


        /* Soft delete government form */
        $governmentform->delete();

        return response()->json([
            'response' => 1,
            'redirectURL' => route('governmentform.show', $governmentform->id),
            'title' => 'Archive Government Form',
            'message' => 'Successfully archived ' . $governmentform->name
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
        $governmentform = governmentform::onlyTrashed()->findOrFail($id);


        /* Restore government form */
        $governmentform->restore();

        return response()->json([
            'response' => 1,
            'redirectURL' => route('governmentform.show', $governmentform->id),
            'title' => 'Restore Government Form',
            'message' => 'Successfully restored ' . $governmentform->name
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
