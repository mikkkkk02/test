<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests\Learnings\LearningStorePost;
use App\Http\Requests\Learnings\LearningAddParticipantPost;

use App\FormTemplate;
use App\FormTemplateCategory;
use App\FormTemplateApprover;

class LearningController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $templates = FormTemplate::select(FormTemplate::MINIMAL_COLUMNS)        
                                ->where('form_template_category_id', FormTemplateCategory::LD)
                                ->with(['approvers' => function($query) {
                                    $query->select(FormTemplateApprover::MINIMAL_COLUMNS);
                                }])
                                ->orderBy('name')
                                ->get();

        $ldForm = FormTemplate::where('form_template_category_id', FormTemplateCategory::LD)->get()->first();
        $ldCreateURL = $ldForm ? route('request.create', $ldForm->id) : null;

        return view('pages.learning.learnings', [
            'templates' => $templates,
            'ldCreateURL' => $ldCreateURL,
            'templateId' => $ldForm->id,
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexMyTeam()
    {
        $categories = \Auth::user()->renderSubordinateFilter();


        return view('pages.learning.learningmyteam', [
            'categories' => $categories,
        ]);
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
    // public function store(LearningStorePost $request)
    // {
    //     $var = $request->except(['isSameTime', 'times', 'start_time', 'end_time']);


    //     /* Create the Learning */
    //     $learning = Learning::create($request);

    //     /* Set times */
    //     $learning->setTime($request->input('isSameTime'), $request->input('times'));

    //     // Notification for admim

    //     return response()->json([
    //         'response' => 1,
    //         'message' => 'Successfully created ' . $learning->name .' learning activity'
    //     ]);         
    // }

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
    // public function update(LearningStorePost $request, $id)
    // {
    //     $learning = Learning::findOrFail($id);
    //     $vars = $request->except(['isSameTime', 'times', 'start_time', 'end_time']);


    //     /* Update the learning */
    //     $learning->update($request);

    //     /* Update times */
    //     $event->setTime($request->input('isSameTime'), $request->input('times'));

    //     return response()->json([
    //         'response' => 1,
    //         'message' => 'Successfully updated learning activity ' . $learning->name
    //     ]);
    // }

    /**
     * Update the specified resource's Employee in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function addParticipants(LearningAddParticipantsPost $request, $id)
    // {
    //     $learning = Learning::findOrFail($id);


    //     /* Update the learning participants */
    //     $learning->addParticipants($request->input('participants'));

    //     return response()->json([
    //         'response' => 1,
    //         'message' => 'Successfully added ' . $learning->name .' employees'
    //     ]);
    // }

    /**
     * Update the specified resource's Employee in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function removeParticipants(LearningRemoveParticipantsPost $request, $id)
    // {
    //     $learning = Learning::findOrFail($id);


    //     /* Update the learning participants */ 
    //     $learning->removeParticipants($request->input('participants'));

    //     return response()->json([
    //         'response' => 1,
    //         'message' => 'Successfully removed ' . $learning->name .' employees'
    //     ]);
    // }    

    /**
     * Update the specified resource's Participant in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function attend(LearningAttendPost $request, $id)
    // {
    //     $learning = Learning::findOrFail($id);
    //     $chargeTo = $request->input('charge_to');


    //     /* Update the learning participant */
    //     $learning->attend($participant, $chargeTo);

    //     // Notification for user & immediate leader

    //     return response()->json([
    //         'response' => 1,
    //         'message' => 'Successfully added ' . $participant->renderFullname() . ' from learning'
    //     ]);
    // }

    /**
     * Remove the specified resource's Participant in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function cancel(LearningCancelPost $request, $id)
    // {
    //     $learning = Learning::findOrFail($id);
    //     $participant = User::findOrFail($request->input('participant_id'));


    //     /* Remove the learning participant */
    //     $learning->cancel($participant);

    //     // Notification for user & immediate leader        

    //     return response()->json([
    //         'response' => 1,
    //         'message' => 'Successfully removed ' . $participant->renderFullname() . ' from learning'
    //     ]);
    // }    

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
