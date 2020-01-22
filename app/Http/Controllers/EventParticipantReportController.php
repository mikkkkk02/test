<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\EventParticipantFetchController;

use Excel;

class EventParticipantReportController extends Controller
{
    /**
     * Instantiate a new EventParticipantReportController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('App\Http\Middleware\Events\ReportEventParticipantsMiddleware', ['only' => ['index', 'export']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = \Auth::user();

        $companies = $user->getHandledCompanies('Generating of BBLS Reports');


        return view('pages.reports.bblsreport', [
            'companies' => $companies,
        ]);
    }


    /**
     * Export a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function export(Request $request)
    {
        $fetch = new EventParticipantFetchController();
        $eventParticipants = $fetch->fetch($request, true);
    

        return Excel::create('bbls_report', function($excel) use ($eventParticipants) {
                $excel->sheet('bbls', function($sheet) use ($eventParticipants) {

                    $data = [];

                    /* Create the excel columns */
                    foreach($eventParticipants as $key => $eventP) {
                        $data[$key] = [
                        	'Date Submitted' => $eventP->created_at,
                            'Employee Name' => $eventP->participant->renderFullname(),
                            'Company' => $eventP->participant->getCompany() ? $eventP->participant->getCompany()->name : '',
                            'Division' => $eventP->participant->getDivision() ? $eventP->participant->getDivision()->name : '',
                            'Department' => $eventP->participant->getDepartment() ? $eventP->participant->getDepartment()->name : '',
                            'Team' => $eventP->participant->getTeam() ? $eventP->participant->getTeam()->name : '',
                            'BBLS Program Title' => $eventP->event->title,
                            'Start Date' => $eventP->event->start_date,
                            'End Date' => $eventP->event->end_date,
                            'Location' => $eventP->event->venue,
                            'Status' => $eventP->renderStatus(),
                            'Approved By' => $eventP->approver->renderFullname(),
                            'Date Approved' => $eventP->approved_at,
                        ];
                    }

                    $sheet->fromArray($data);
                });
            })->download('xlsx');
    }    
}
