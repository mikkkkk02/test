<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\IDPFetchController;

use Excel;

use App\IdpCompetency;

class IDPReportController extends Controller
{
    /**
     * Instantiate a new IDPController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('App\Http\Middleware\IDPs\ReportIDPMiddleware', ['only' => ['index', 'export']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = \Auth::user();

        $IdpCompetencies = IdpCompetency::select(IdpCompetency::MINIMAL_COLUMNS)->get();
        $companies = $user->getHandledCompanies('Generating of L&D Reports');


        return view('pages.idps.idpreport', [
            'specificcompetencies' => $IdpCompetencies,
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
        $fetch = new IDPFetchController();
        $idps = $fetch->fetch($request, true);
    

        return Excel::create('idp_report', function($excel) use ($idps) {
                $excel->sheet('idps', function($sheet) use ($idps) {

                    $data = [];

                    /* Create the excel columns */
                    foreach($idps as $key => $idp) {
                        $data[$key] = [
                            'Name' => $idp->employee->renderFullname(),
                            'Team' => $idp->employee->department ? $idp->employee->department->team->name : '',
                            'Position' => $idp->employee->department ? $idp->employee->department->position->title : '',
                            'Immediate Leader' => $idp->employee->supervisor ? $idp->employee->supervisor->renderFullname() : '',
                            'Learning Act. Type' => $idp->renderLearningActivityType(),
                            'Competency Type' => $idp->renderCompetencyType(),
                            'Specific Competency' => $idp->competency->name,
                            'Details' => $idp->details,
                            'Completion Year' => $idp->completion_year,
                            'Status' => $idp->renderStatus()
                        ];
                    }

                    $sheet->fromArray($data);
                });
            })->download('xlsx');
    }
}
