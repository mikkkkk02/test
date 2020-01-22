<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\RequestFetchController;

use Excel;

use App\Form;
use App\FormTemplate;
use App\FormTemplateCategory;

class RequestReportController extends Controller
{
    /**
     * Instantiate a new RequestController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('App\Http\Middleware\Requests\ReportAdminRequestMiddleware', ['only' => ['adminIndex']]);

        $this->middleware('App\Http\Middleware\Requests\ReportHRRequestMiddleware', ['only' => ['hrIndex']]);

        $this->middleware('App\Http\Middleware\Requests\ReportLDRequestMiddleware', ['only' => ['ldIndex']]);

        $this->middleware('App\Http\Middleware\Requests\ExportRequestMiddleware', ['only' => ['export']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function adminIndex()
    {
        $user = \Auth::user();

        $companies = $user->getHandledCompanies('Generating of Admin Reports');
        $templates = FormTemplate::select(FormTemplate::MINIMAL_COLUMNS)->where('type', FormTemplate::ADMIN)->orderBy('name')->get();


        return view('pages.requests.adminrequestreport', [
            'companies' => $companies,
            'templates' => $templates,
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function hrIndex()
    {
        $user = \Auth::user();

        $companies = $user->getHandledCompanies('Generating of HR Reports');
        $templates = FormTemplate::select(FormTemplate::MINIMAL_COLUMNS)->where('type', FormTemplate::HR)->orderBy('name')->get();


        return view('pages.requests.hrrequestreport', [
            'companies' => $companies,
            'templates' => $templates,
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function ldIndex()
    {
        $user = \Auth::user();

        $companies = $user->getHandledCompanies('Generating of L&D Reports');
        $templates = FormTemplateCategory::find(FormTemplateCategory::LD)->form_templates()->orderBy('name')->get();


        return view('pages.requests.ldrequestreport', [
            'companies' => $companies,
            'templates' => $templates,
        ]);
    }

    /**
     * Export a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function export(Request $request)
    {
        $fetch = new RequestFetchController();
        $requests = $fetch->fetch($request, true);

        $data = $this->fetchData($requests);
    

        return Excel::create('request_report', function($excel) use ($data) {
                $excel->sheet('requests', function($sheet) use ($data) {

                    $sheet->fromArray($data);

                });
            })->download('xlsx');
    }


    private function fetchData($requests) {
        $data = [];
        $count = 0;
        $addTravelOrderCol = false;

        /* Create the excel columns */
        foreach($requests as $key => $request) {

            $data[$count] = [
                'Request Number' => $request->id,
                'Ticket Number' => $request->ticket ? $request->ticket->id : '',
                'Date Submitted' => $request->created_at,
                'Employee Name' => $request->employee->renderFullname(),
                'Company' => $request->employee->getCompany() ? $request->employee->getCompany()->name : '',
                'Division' => $request->employee->getDivision() ? $request->employee->getDivision()->name : '',
                'Department' => $request->employee->getDepartment() ? $request->employee->getDepartment()->name : '',
                'Team' => $request->employee->getTeam() ? $request->employee->getTeam()->name : '',
                'Type of Service Request' => $request->template->name,
                'Details' => $request->renderAnswers(),
                'Updates' => $request->renderUpdates(),
                'Approved By' => '',
                'Approver Reason' => '',
                'Date Approved' => $request->ticket ? $request->ticket->created_at : '',
                'SLA' => $request->template->sla,
                'SLA Compliance' => $request->ticket ? $request->ticket->renderComputedState() : '',
                'Assigned To' => $request->ticket && $request->ticket->technician ? $request->ticket->technician->renderFullname() : '',
                'Status' => $request->ticket ? $request->ticket->renderStatus() : $request->renderStatus(),
                'Date Needed' => $request->ticket ? $request->ticket->renderDeadline() : '',
                'Date Closed' => $request->ticket ? $request->ticket->date_closed : '',
            ];


            $approverCount = 0;
            foreach($request->approvers as $approverKey => $approver) {
                /* Add in approver details */
                $data = $this->addColToData($data, [
                            'Approved By' => $approver->approver->renderFullname(), 
                            'Approver Reason' => $approver->reason,
                        ], $count + $approverCount);

                $approverCount++;
            }

            /*
            | Add in special fields for the diff. categories
            |------------------------------------------------*/
            $travelOrderCount = 0;
            if($request->template->isTravelOrder()) {
                /* Check if request already have ticket */
                if($request->ticket) {
                    foreach($request->ticket->travel_order_details as $travelKey => $travelDetail) {
                        /* Add in travel details */
                        $data = $this->addColToData($data, [
                                    'Accommodation' => $travelDetail->accommodation,
                                    'Transportation Type' => $travelDetail->transportation_type,
                                    'Travel Details' => $travelDetail->details,
                                    'Remarks' => $travelDetail->remarks,
                                ], $count + $travelOrderCount);

                        $travelOrderCount++;
                    }
                }

            } else if($request->template->category->forLearning()) {

                /* Add in travel details */
                $data = $this->addColToData($data, [
                            'Training Venue' => $request->isLocal ? 'Local' : 'International',
                            'Course Cost' => $request->course_cost,
                            'Accomodation Cost' => $request->accommodation_cost,
                            'Meal Cost' => $request->meal_cost,
                            'Transport Cost' => $request->transport_cost,
                            'Others Cost' => $request->others_cost,
                            'Total Cost' => $request->total_cost,
                        ], $count);
            }

            /* Increment the count */
            $count = $count + max([$approverCount, $travelOrderCount]) + 1;
        }


        return $data;
    }

    private function addColToData($data, $addedFields, $index) {

        /* Add in default columns */
        if(!isset($data[$index])) {
            $data[$index] = [
                'Request Number' => isset($data[$index]) ? $data[$index]['Request Number'] : '',  
                'Ticket Number' => isset($data[$index]) ? $data[$index]['Ticket Number'] : '',  
                'Date Submitted' => isset($data[$index]) ? $data[$index]['Date Submitted'] : '',  
                'Employee Name' => isset($data[$index]) ? $data[$index]['Employee Name'] : '',  
                'Company' => isset($data[$index]) ? $data[$index]['Company'] : '',  
                'Division' => isset($data[$index]) ? $data[$index]['Division'] : '',  
                'Department' => isset($data[$index]) ? $data[$index]['Department'] : '',  
                'Team' => isset($data[$index]) ? $data[$index]['Team'] : '',
                'Type of Service Request' => isset($data[$index]) ? $data[$index]['Type of Service Request'] : '',  
                'Details' => isset($data[$index]) ? $data[$index]['Details'] : '',  
                'Updates' => isset($data[$index]) ? $data[$index]['Updates'] : '',  
                'Approved By' => isset($data[$index]) ? $data[$index]['Approved By'] : '',  
                'Approver Reason' => isset($data[$index]) ? $data[$index]['Approver Reason'] : '',
                'Date Approved' => isset($data[$index]) ? $data[$index]['Date Approved'] : '',  
                'SLA' => isset($data[$index]) ? $data[$index]['SLA'] : '',  
                'Assigned To' => isset($data[$index]) ? $data[$index]['Assigned To'] : '',  
                'Status' => isset($data[$index]) ? $data[$index]['Status'] : '',  
                'Date Closed' => isset($data[$index]) ? $data[$index]['Date Closed'] : '',
            ];
        }

        /* Add in special columns */
        foreach($addedFields as $key => $field) {
            /* Check if column already exists */
            if(!isset($data[0][$key]))
                $data[0][$key] = '';

            /* Add column value */
            $data[$index][$key] = $field;
        }

        return $data;
    }
}
