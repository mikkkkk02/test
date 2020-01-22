<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\Forms\FormStorePost;

use Carbon\Carbon;

use App\MrReservation;
use App\MrReservationTime;
use App\Location;
use App\FormTemplate;
use App\Form;
use App\FormTemplateField;
use App\FormTemplateOption;

class MrReservationController extends Controller
{
    public function __construct()
    {
        $this->middleware('App\Http\Middleware\MrReservations\MrReservationIndexMiddleware', ['only' => ['index', 'create']]);
        
        $this->middleware('App\Http\Middleware\MrReservations\EditMrReservationMiddleware', ['only' => ['store', 'update', 'archive', 'restore']]);

        $this->middleware('App\Http\Middleware\MrReservations\ViewMrReservationMiddleware', ['only' => ['show']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $formTemplates = FormTemplate::where('request_type', FormTemplate::MEETINGROOM)->get();

        $mrTimeCalendarObjects = MrReservationTime::toCalendarObject(MrReservationTime::get());

        return view('pages.mrreservations.mrreservations', [
            'formTemplates' => $formTemplates,
            'mrTimeCalendarObjects' => $mrTimeCalendarObjects,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $locations = Location::select(Location::TABLE_COLUMNS)->with('rooms')->get();

        if (!$request->has('formtemplate')) {
            return redirect()->route('mrreservations');
        }
        
        $formTemplate = FormTemplate::select(FormTemplate::MINIMAL_COLUMNS)
                                    ->with('fields', 'fields.options')
                                    ->findOrFail($request->input('formtemplate'));

        if ($formTemplate->request_type !== FormTemplate::MEETINGROOM) {
            return redirect()->route('mrreservations');
        }

        return view('pages.mrreservations.createmrreservation', [
            'formTemplate' => $formTemplate,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(FormStorePost $request)
    {
        /**
         * Begin Transaction
         */
        \DB::beginTransaction();

        
        $mrReservation = MrReservation::submit($request);


        /**
         * End Transaction
         */
        \DB::commit();

        return response()->json([
            'response' => 1,
            'redirectURL' => route('mrreservation.show', $mrReservation->id),
            'title' => 'Create meeting room',
            'message' => 'Successfully created Meeting Room Reservation ' . $mrReservation->name
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\MrReservation  $mrReservation
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $mrReservation = MrReservation::withTrashed()->with(['mr_reservation_times', 'room', 'room.location'])->find($id);

        $form = $mrReservation->form;
        $formTemplate = $mrReservation->template()->select(FormTemplate::MINIMAL_COLUMNS)->with('fields', 'fields.options')->first();

        $answers = $form->answers;
        $approvers = $form->approvers;
        $updates = $form->updates;
        $history = $form->history;

        $types = FormTemplateField::getTypes();
        $tableTypes = FormTemplateOption::getTypes();

        return view('pages.mrreservations.showmrreservation', [
            'mrReservation' => $mrReservation,
            'form' => $form,
            'formTemplate' => $formTemplate,
            'answers' => $answers,
            'approvers' => $approvers,
            'updates' => $updates,
            'histories' => $history,
            'types' => $types,
            'tableTypes' => $tableTypes,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\MrReservation  $mrReservation
     * @return \Illuminate\Http\Response
     */
    public function edit(MrReservation $mrReservation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\MrReservation  $mrReservation
     * @return \Illuminate\Http\Response
     */
    public function update(FormStorePost $request, $id)
    {        
        /**
         * Begin Transaction
         */
        \DB::beginTransaction();


        $mrReservation = MrReservation::submit($request, $id, false, false);


        /**
         * Commit
         */
        \DB::commit();

        return response()->json([
            'response' => 1,
            'redirectURL' => route('mrreservation.show', $mrReservation->id),
            'title' => 'Update room details',
            'message' => 'Successfully updated room ' . $mrReservation->name,
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
        $mrReservation = MrReservation::findOrFail($id);

        $mrReservation->archive();

        return response()->json([
            'response' => 1,
            'redirectURL' => route('mrreservation.show', $mrReservation->id),
            'title' => 'Archive Meeting Room Reservation',
            'message' => 'Successfully archived ' . $mrReservation->name
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
        $mrReservation = MrReservation::onlyTrashed()->findOrFail($id);


        /* Restore group */
        $mrReservation->unarchive();

        return response()->json([
            'response' => 1,
            'redirectURL' => route('mrreservation.show', $mrReservation->id),
            'title' => 'Restore Meeting Room Reservation',
            'message' => 'Successfully restored ' . $mrReservation->name
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\MrReservation  $mrReservation
     * @return \Illuminate\Http\Response
     */
    public function destroy(MrReservation $mrReservation)
    {
        //
    }
}
