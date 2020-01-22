<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests\ImportPost;

use Excel;

use App\User;
use App\Idp;
use App\IdpCompetency;

class IDPImportController extends Controller
{

    /**
     * Instantiate a new UserImportController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('App\Http\Middleware\IDPs\ImportIDPMiddleware', ['only' => ['import']]);
    }

    /**
     * Import a resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function import(ImportPost $request)
    {

        /* Check if there is a file uploaded */
        if($request->hasFile('file')) {

            $overwrite = $request->input('overwrite');

            $file = $request->file('file');
            $path = $file->getRealPath();
            $rowArray = [];


            /* Process excel data array */
            Excel::load($path, function($reader) use (&$rowArray) {

                /* Fetch data array */
                $rowArray = $reader->all()->toArray();
            });


            /* Check if file has all needed columns */
            return $this->checkImportColumn($rowArray, $overwrite);
        }
    }

    /**
     * Check uploaded file's columns
     *
     * @param  Array  $data
     * @return \Illuminate\Http\Response
     */
    public function checkImportColumn($data, $overwrite)
    {
        $important = ['employee_number', 'specific_competency', 'learning_activity_type', 'competency_type', 'required_proficiency_level', 'current_proficiency_level', 'activity_details', 'target_completion_year'];

        foreach($important as $key => $value) {
            
            if(!array_key_exists($important[$key], $data[0])) {
                return response()->json([
                    'response' => 0,
                    'message' => "Column " . str_replace('_', ' ', $important[$key]) . " is missing from the imported file",
                    'logs' => ['success' => '', 'errors' => ''],
                ]);
            }
        }

        /* Update or create IDP data */
        return $this->updateOrCreate($data, $overwrite);
    }

    /**
     * Import a resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateOrCreate($data, $overwrite)
    {
        /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();


        /* Fetch user */
        $this->user = \Auth::user();

        /* Fetch handled companies */
        $companies = $this->user->getHandledCompanies('Adding/Editing of Employee Profile');
        $this->companies = array_map(function($ar) {
                                return $ar['id'];
                            }, is_array($companies) ? $companies : $companies->toArray());

        $logs = ['success' => '', 'errors' => ''];
        $method = '';


        /* Loop through each sheet */
        foreach($data as $key => $row) {

            /* Check if employee no data exists */
            if(isset($row['employee_number'])) {

                /* Fetch the employee */
                $empNo = $row['employee_number'];
                $employee = User::withTrashed()->find($empNo);

                /* Check if employee exists */
                if($employee) {

                    /* Fetch the company */
                    $company = $employee->getCompany();
                    $comCollection = $company ? Collect($company->id) : false;

                    /* Check permission to edit company */
                    if($this->user->isSuperUser() || $comCollection && $comCollection->intersect($this->companies)->count()) {
                        /* Check if employee exists */
                        if($employee) {

                            $vars = [
                                'employee_id' => $employee->id,
                                'competency_id' => IdpCompetency::updateOrCreate(['name' => $row['specific_competency']])->id,

                                'learning_type' => Idp::getConstantValue(Idp::getLearningActivityType(), $row['learning_activity_type']),
                                'competency_type' => Idp::getConstantValue(Idp::getCompetencyType(), $row['competency_type']),
                                'required_proficiency_level' => $row['required_proficiency_level'] ? $row['required_proficiency_level'] : 0,
                                'current_proficiency_level' => $row['current_proficiency_level'] ? $row['current_proficiency_level'] : 0,

                                'details' => $row['activity_details'],

                                'completion_year' => $row['target_completion_year'],
                            ];

                            $idp = Idp::where($vars)->get();


                            /* Check if it exists */
                            if($idp->count()) {
                                /* Check if overwrite is enabled */
                                if($overwrite) {

                                    $idp = $idp->first();

                                    /* Add additional vars */
                                    $vars['updater_id'] = $this->user->id;


                                    /* Update IDP */
                                    $idp->update($vars);

                                    $logs['success'][] = "* Line " . ($key + 2) . ": Successfully updated <a href='" . $employee->renderViewURL() . "'>" . $employee->renderFullname() . "'s</a> IDP <a href='" . $idp->renderViewURL() . "'>#" . $idp->id . "</a> \n";

                                } else {

                                    $logs['errors'][] = "* Line " . ($key + 2) . ": Ignoring existing IDP for overwrite option is disabled" . "\n";
                                }

                            } else {

                                /* Add additional vars */
                                $vars['creator_id'] = $this->user->id;
                                $vars['updater_id'] = $this->user->id;


                                /* Create IDP */
                                $idp = Idp::create($vars);

                                $logs['success'][] = "* Line " . ($key + 2) . ": Successfully created IDP <a href='" . $idp->renderViewURL() . "'>#" . $idp->id . "</a> for <a href='" . $employee->renderViewURL() . "'>" . $employee->renderFullname() . "</a> \n";
                            }

                        } else {

                            $logs['errors'][] = "* Line " . ($key + 2) . ": Employee no. " . $empNo . " doesn't exist, please check if the employee no. is correct." . "\n";
                        }

                    } else {

                        $logs['errors'][] = "* Line " . ($key + 2) . ": Unauthorized access to add/edit to the Company " . $company->name . "\n";
                    }

                } else {

                    $logs['errors'][] = "* Line " . ($key + 2) . ": Employee no. " . $empNo . " not found \n";
                }                                
            }
        }


        /*
        | @End Transaction
        |---------------------------------------------*/
        \DB::commit();


        return response()->json([
            'response' => 1,
            'message' => 'Successfully updated IDP records base on the uploaded file',
            'logs' => $logs,
        ]);
    }
}
