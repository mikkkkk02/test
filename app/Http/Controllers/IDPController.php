<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests\Learnings\IDPStorePost;
use App\Http\Requests\Learnings\IDPDeleteAllPost;
use App\Http\Requests\Learnings\IDPApproveTempPost;
use App\Http\Requests\Learnings\IDPDisapproveTempPost;

use App\Notifications\Learnings\IDPWasUpdated;

use App\Idp;
use App\IdpApprover;
use App\TempIdp;
use App\IdpCompetency;
use App\User;

class IDPController extends Controller
{
    /**
     * Instantiate a new IDPController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('App\Http\Middleware\IDPs\ViewIDPMiddleware', ['only' => ['show', 'update']]);

        $this->middleware('App\Http\Middleware\IDPs\DeleteIDPMiddleware', ['only' => ['destroy', 'destroyAll', 'transfer']]);

        $this->middleware('App\Http\Middleware\IDPs\ViewTempIDPMiddleware', ['only' => ['showTemp']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $employees = User::select(User::MINIMAL_COLUMNS)->orderBy('last_name', 'asc')->get();


        return view('pages.idps.idps', [
            'employees' => $employees,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $employees = User::select(User::MINIMAL_COLUMNS)->orderBy('last_name', 'asc')->get();
        $competencies = IdpCompetency::select(IdpCompetency::MINIMAL_COLUMNS)->get();


        return view('pages.idps.createidp', [
            'employees' => $employees,
            'competencies' => $competencies,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $vars = $request->except([]);
        $vars['creator_id'] = \Auth::user()->id;
        $vars['updater_id'] = \Auth::user()->id;        


        /*
        | @Begin
        |---------------------------------------------*/
        \DB::beginTransaction();


        /* Create IDP */
        $idp = Idp::create($vars);
        $idp->addApprovers(1);


        /*
        | @End Transaction
        |---------------------------------------------*/
        \DB::commit();


        return response()->json([
            'response' => 1,
            'redirectURL' => route('idp.show', $idp->id),
            'title' => 'Create IDP',
            'message' => 'Successfully created idp ' . $idp->id
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
        $idp = Idp::findOrFail($id);
        $approvers = $idp->approvers;

        $employees = User::select(User::MINIMAL_COLUMNS)->orderBy('last_name', 'asc')->get();
        $competencies = IdpCompetency::select(IdpCompetency::MINIMAL_COLUMNS)->get();


        return view('pages.idps.showidp', [
            'idp' => $idp,
            'approvers' => $approvers,            
            'employees' => $employees,
            'competencies' => $competencies,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showTemp($id)
    {
        $idpTmp = TempIdp::withTrashed()->findOrFail($id);
        $approvers = $idpTmp->idp->approvers;

        $competencies = IdpCompetency::select(IdpCompetency::MINIMAL_COLUMNS)->get();


        return view('pages.idps.showidptmp', [
            'idp' => $idpTmp,
            'approvers' => $approvers,
            'temp' => true,
            'competencies' => $competencies,
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
    public function update(IDPStorePost $request, $id)
    {
        $idp = Idp::findOrFail($id);
        $user = \Auth::user();
        $message = '';


        /*
        | @Begin
        |---------------------------------------------*/
        \DB::beginTransaction();


        /* If the IDP still needs approval */
        if($idp->employee->id == $user->id) { 

            /* Update Temp IDP */
            $tempIDP = $idp->tempUpdate($request);

            $message = 'Update is now sent for approval';

        } else {

            $vars = $request->except([]);
            $vars['updater_id'] = $user->id;

            /* Update IDP */
            $idp->update($vars);

            $message = 'Successfully updated idp ' . $idp->id;
        }


        /*
        | @End Transaction
        |---------------------------------------------*/
        \DB::commit();


        return response()->json([
            'response' => 1,
            'redirectURL' => route('idps'),
            'title' => 'Update IDP',
            'message' => $message,
        ]);
    }

    /**
     * Approve the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function approve(Request $request, $id)
    {
        $idp = Idp::findOrFail($id);
        $isUpdate = $idp->temp ? ' update' : ' request';


        /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();


        /* Approve the request */
        if($idp->updateStatus(IdpApprover::APPROVED)) {


            /*
            | @End Transaction
            |---------------------------------------------*/
            \DB::commit();


            return response()->json([
                'response' => 1,
                'redirectURL' => route('learning.myteam'),
                'title' => 'Approve IDP' . $isUpdate,
                'message' => 'Successfully approved idp ' . $idp->id . $isUpdate
            ]);
        }


        /*
        | @End Transaction
        |---------------------------------------------*/
        \DB::commit();


        return response(['Permission' => 'Unauthorized request status update'], 422);
    }

    /**
     * Disapprove the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function disapprove(IDPDisapproveTempPost $request, $id)
    {
        $idp = Idp::findOrFail($id);
        $isUpdate = $idp->temp ? ' update' : ' request';


        /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();


        /* Approve the request */
        if($idp->updateStatus(IdpApprover::DISAPPROVED)) {


            /*
            | @End Transaction
            |---------------------------------------------*/
            \DB::commit();


            return response()->json([
                'response' => 1,
                'redirectURL' => route('learning.myteam'),
                'title' => 'Disapprove IDP' . $isUpdate,
                'message' => 'Successfully disapproved idp ' . $idp->id . $isUpdate
            ]);
        }


        /*
        | @End Transaction
        |---------------------------------------------*/
        \DB::commit();


        return response(['Permission' => 'Unauthorized request status update'], 422);
    }        

    /**
     * Transfer approval to the specified resource's supervisor.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function transfer(Request $request, $id)
    {
        $tempIDP = TempIdp::findOrFail($id);
        $idp = $tempIDP->idp;


        /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();


        /* Transfer approval to supervisor */
        $tempIDP->transferApproval();


        /*
        | @End Transaction
        |---------------------------------------------*/
        \DB::commit();


        return response()->json([
            'response' => 1,
            // 'redirectURL' => route('idptmp.show', $tempIDP->id),
            'title' => 'Transfer Approval',
            'message' => 'Successfully transferred approval of ' . $idp->id . ' to ' . $tempIDP->approver->renderFullname(),
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
        $idp = Idp::findOrFail($id);


        /* Delete the IDP */
        $idp->delete();

        return response()->json([
            'response' => 1,
            'redirectURL' => route('idps'),
            'title' => 'Delete IDP',
            'message' => 'Successfully deleted idp ' . $idp->id,
        ]);          
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroyAll(IDPDeleteAllPost $request)
    {
        $count = 0;
        $idps = $request->input('idps');


        /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();


        /* Soft delete form template */
        foreach($idps as $key => $value) {

            /* Fetch and delete */
            $idp = Idp::find($value);
            $idp->delete();

            $count++;
        }


        /*
        | @End Transaction
        |---------------------------------------------*/
        \DB::commit();
        

        return response()->json([
            'response' => 1,
            'redirectURL' => route('idps'),
            'title' => 'Delete IDPs',
            'message' => 'Successfully deleted ' . $count . ' IDPs',
        ]);          
    }
}
