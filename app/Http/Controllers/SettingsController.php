<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests\Settings\SettingsDelegatePost;
use App\Http\Requests\Settings\SettingsUpdatePost;
use App\Http\Requests\Settings\SettingsTicketPost;

use App\Notifications\Requests\RequestHasApprover;

use App\User;
use App\Company;
use App\Form;
use App\FormApprover;
use App\FormTemplateApprover;
use App\Settings;

class SettingsController extends Controller
{
    /**
     * Instantiate a new SettingsController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('App\Http\Middleware\Settings\EditSettingsMiddleware', ['only' => ['update']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = \Auth::user();
        $settings = Settings::get()->first(); http_response_code(500);

        $employees = User::select(User::MINIMAL_COLUMNS)->orderBy('last_name', 'asc')->get();
        $companies = Company::select(Company::MINIMAL_COLUMNS)->get();


        return view('pages.settings.settings', [
            'settings' => $settings,
            'employees' => $employees,
            'companies' => $companies,
        ]);
    }

    /**
     * Update the users assignee.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delegate(SettingsDelegatePost $request)
    {
        $user = \Auth::user();


        /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();

        
        /* Update the user's assignee */
        $user->updateAssignee($request->input('employees'));


        /*
        | @End Transaction
        |---------------------------------------------*/
        \DB::commit();


        return response()->json([
            'response' => 1,
            'title' => 'Update assignee',
            'message' => 'Successfully updated assignee'
        ]);        
    }

    /**
     * Update the special technician per company position.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function ticket(SettingsTicketPost $request)
    {
        $settings = Settings::get()->first();
        $companies = Company::get();

        
        /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();        


        foreach($companies as $key => $company) {
            
            /* Check and assign company admins*/
            if($request->has('admin_technician_id') && isset($request->input('admin_technician_id')[$company->id])) {

                $technicianID = $request->input('admin_technician_id')[$company->id];

                /* Check if there is a technician assigned */
                if($technicianID)
                    $company->updateAdminTechnicians($technicianID);
            }
            
            /* Check and assign company admins*/
            if($request->has('hr_technician_id') && isset($request->input('hr_technician_id')[$company->id])) {

                $technicianID = $request->input('hr_technician_id')[$company->id];

                /* Check if there is a technician assigned */
                if($technicianID)
                    $company->updateHRTechnicians($technicianID);
            }
            
            /* Check and assign company admins*/
            if($request->has('od_technician_id') && isset($request->input('od_technician_id')[$company->id])) {

                $technicianID = $request->input('od_technician_id')[$company->id];

                /* Check if there is a technician assigned */
                if($technicianID)
                    $company->updateODTechnicians($technicianID);
            }                       
        }


        /*
        | @End Transaction
        |---------------------------------------------*/
        \DB::commit();


        return response()->json([
            'response' => 1,
            'title' => 'Update ticket settings',
            'message' => 'Successfully updated ticket settings'
        ]);        
    } 

    /**
     * Update the special company position.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(SettingsUpdatePost $request)
    {
        $settings = Settings::get()->first();
        $companies = Company::get();        

        
        /*
        | @Update CEO ID & approvals
        |-----------------------------------------------*/
        $ceoID = $request->input('ceo_id');

        /* Store old CEO ID */
        $tmpCeoID = $settings->ceo_id;

        /* Update the emplotees */
        $settings->ceo_id = $ceoID;
        $settings->save();

        /* Check if assigned user changed, update approvals if so */
        if($tmpCeoID != $ceoID)
            $this->updateCEOApprovers($tmpCeoID, $ceoID);


        foreach($companies as $key => $company) {
                            
            /*
            | @Update HR ID & approvals
            |-----------------------------------------------*/
            if($request->has('hr_id') && isset($request->input('hr_id')[$company->id])) {

                $hrID = $request->input('hr_id')[$company->id];

                /* Check if there is a technician assigned */
                if($hrID) {

                    /* Store old company HR ID */
                    $tmpHrID = $company->hr_id;

                    $company->hr_id = $hrID;
                    $company->save();


                    /* Check if assigned user changed, update approvals if so */
                    if($tmpHrID != $hrID)
                        $this->updateCompanyApprovers($company, FormTemplateApprover::HR, $tmpHrID, $hrID);
                }
            }
            

            /*
            | @Update OD ID & approvals
            |-----------------------------------------------*/
            if($request->has('od_id') && isset($request->input('od_id')[$company->id])) {

                $odID = $request->input('od_id')[$company->id];

                /* Check if there is a technician assigned */
                if($odID) {

                    /* Store old company OD ID */
                    $tmpOdID = $company->od_id;

                    $company->od_id = $odID;
                    $company->save();


                    /* Check if assigned user changed, update approvals if so */
                    if($tmpOdID != $odID)
                        $this->updateCompanyApprovers($company, FormTemplateApprover::OD, $tmpOdID, $odID);
                }
            }
        }


        return response()->json([
            'response' => 1,
            'title' => 'Update settings',
            'message' => 'Successfully updated settings'
        ]);        
    }

    public function updateCEOApprovers($oldApprover, $newApprover) {

        /* Fetch all included form approvers */
        $formApprovers = FormApprover::where('status', FormApprover::PENDING)
                                        ->where('type', FormTemplateApprover::CEO)
                                        ->where('approver_id', $oldApprover)
                                        ->get();

                                        
        /* Update each request and send in the notification */
        foreach($formApprovers as $key => $formApprover) {

            /* Update the approver */
            $this->updateApprover($formApprover, $newApprover);
        }
    }

    public function updateCompanyApprovers($company, $position, $oldApprover, $newApprover) {

        /* Fetch form IDs base on template */
        $formTempIDs = FormTemplateApprover::where('type', FormTemplateApprover::COMPANY)
                                            ->where('type_value', $position)
                                            ->pluck('id')
                                            ->toArray();

        /* Fetch all included form approvers */
        $formApprovers = FormApprover::whereIn('form_template_approver_id', $formTempIDs)
                                        ->where('status', FormApprover::PENDING)
                                        ->get();

        /* Fetch all automated included form approvers */
        $autoFormApprovers = FormApprover::where('type', FormTemplateApprover::COMPANY)
                                        ->where('type_value', $position)
                                        ->where('status', FormApprover::PENDING)
                                        ->get();
                                        
                                        
        /* Update each request and send in the notification */
        foreach($formApprovers as $key => $formApprover) {

            /* Check if employee is affected on the change */
            $tmpCompany = $formApprover->form->employee->getCompany();
            if($tmpCompany->id == $company->id) {

                /* Update the approver */
                $this->updateApprover($formApprover, $newApprover);
            }
        }

        /* Update each request and send in the notification */
        foreach($autoFormApprovers as $key => $formApprover) {

            /* Check if employee is affected on the change */
            $tmpCompany = $formApprover->form->employee->getCompany();
            if($tmpCompany->id == $company->id) {

                /* Update the approver */
                $this->updateApprover($formApprover, $newApprover);
            }
        }        
    }

    private function updateApprover($formApprover, $newApprover) {
        $newApprover = User::find($newApprover);

        /* Check if new approver exists */
        if($newApprover) {
            
            /* Update approver */
            $formApprover->approver_id = $newApprover->id;
            $formApprover->save();

            /* Check if notification is needed */
            if($formApprover->enabled)
                $formApprover->approver->notify(new RequestHasApprover($formApprover->form, $formApprover));        
        }
    }    
}
