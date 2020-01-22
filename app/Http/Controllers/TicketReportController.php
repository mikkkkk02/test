<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Carbon\Carbon;

use App\User;
use App\Company;
use App\Ticket;

class TicketReportController extends Controller
{
    protected $ticketIDs = null;

    /**
     * Generate data for the Percentage component
     *
     * @return \Illuminate\Http\Response
     */
	public function generatePercent(Request $request) {
        $data = [];
        $where = [];

        /* Set needed variables*/
        $startDate = Carbon::parse($request->startDate);
        $endDate = Carbon::parse($request->endDate);

        /* Set WhereIn */
        $this->getTickets();

        /* Set Where */
        array_push($where, ['created_at', '>=', $startDate]);
        array_push($where, ['created_at', '<=', $endDate]);
        array_push($where, ['status', '!=', Ticket::CANCELLED]);


		/* Fetch all */
    	$total = Ticket::whereIn('id', $this->ticketIDs)
                        ->where($where)->get()->count();

    	$data[] = array(
    		'id' => 0,
    		'value' => '',
    		'label' => 'Total Requests',
    		'color' => 'text-aqua',
    		'percent' => $total,
            'image' => '/image/charts/chart1.png',
    	);


		/* Loop through each status */
        foreach(Ticket::getStates() as $key => $value) {
            $where = [];

            /* Set Where */
            array_push($where, ['created_at', '>=', $startDate]);
            array_push($where, ['created_at', '<=', $endDate]);
            array_push($where, ['status', '!=', Ticket::CANCELLED]);
            /* Add additional where condition for each state */
            array_push($where, ['state', $value['value']]);


        	/* Get data */
        	$count = Ticket::whereIn('id', $this->ticketIDs)
                            ->where($where)->get()->count();

        	$data[] = array(
        		'id' => $value['value'],
        		'value' => $count,
        		'label' => $value['label'],
        		'color' => $value['class'],
                'percent' => $this->percent($count, $total),
        		'image' => $value['image'],
        	);
        }		

		return response()->json([
			'response' => 1,
			'data' => $data,
		]);	
	}

    /**
     * Generate data for the Pie Chart component
     *
     * @return \Illuminate\Http\Response
     */
	public function generatePiechart(Request $request) {
        $data = [];

        /* Set needed variables*/
        $startDate = Carbon::parse($request->startDate);
        $endDate = Carbon::parse($request->endDate);

        /* Set WhereIn */
        $this->getTickets();

		/* Loop through each status */
        foreach(Ticket::getStatus() as $key => $value) {
            $where = [];

            /* Set Where */
            array_push($where, ['created_at', '>=', $startDate]);
            array_push($where, ['created_at', '<=', $endDate]);
            /* Add additional where condition for each status */
            array_push($where, ['status', $value['value']]);


        	/* Get data */
        	$count = Ticket::whereIn('id', $this->ticketIDs)
                            ->where($where)->get()->count();

        	$data[] = array(
        		'value' => $count,
        		'color' => $value['color'],
        		'highlight' => $value['color'],
        		'label' => $value['label'],
        	);
        }


        return response()->json([
        	'response' => 1,
        	'data' => $data,
        ]);
	} 

    /**
     * Generate data for the Bar chart component
     *
     * @return \Illuminate\Http\Response
     */
	public function generateBarchart(Request $request) {
        $data = [];
        $where = [];
        $labels = [];
        $total = [];
        $within = [];

        /* Set needed variables*/
        $startDate = Carbon::parse($request->startDate);
        $endDate = Carbon::parse($request->endDate);

        /* Set WhereIn */
        $this->getTickets();

		/* Loop through each month of the selected year */
		while($startDate->lte($endDate)) {
			$where = [];

			/* Set needed variables*/
			$current = Carbon::parse($startDate);
			$currentLast = Carbon::parse($current)->lastOfMonth();

            /* Set label */
            $labels[] = Carbon::parse($current)->format('F');

            /* Set Where */
            array_push($where, ['created_at', '>=', $current]);
            array_push($where, ['created_at', '<=', $currentLast]);

            /*
            | @Create total requests data
            |-----------------------------------------------*/
			$total[] = Ticket::whereIn('id', $this->ticketIDs)
                                ->where($where)->get()->count();            


            /*
            | @Create within SLA data
            |-----------------------------------------------*/
            array_push($where, ['state', Ticket::ONTIME]);
			$within[] = Ticket::whereIn('id', $this->ticketIDs)
                                ->where($where)->get()->count(); 


            /* Increment month */
            $startDate->addMonths(1);
		}

        return response()->json([
        	'response' => 1,
        	'labels' => $labels,
        	'total' => $total,
        	'within' => $within,
        ]);
	}

    /**
     * Fetches all tickets that is handled by the current user
     */    
    public function getTickets() {

        /* Fetch all included ticket IDs */
        $ticketIDs = $this->getIncludedTicketIDs();


        /* Include employee tickets */        
        if($employeeIDs = $this->getIncludedEmployeeIDs())
            $ticketIDs = array_merge($ticketIDs, $employeeIDs);


        $this->ticketIDs = array_unique($ticketIDs);
    } 

    /**
     * Fetches all employee IDs handled by the user's role
     */ 
    protected function getIncludedEmployeeIDs() {
        $user = \Auth::user();

        /* Set necessary data */
        $companies = $this->getCompanyIDs($user, ['Editing/Removing of Tickets', 'Updating of Ticket Status', 'Generating of Ticketing Reports']);
        $ids = [];

        foreach($companies as $key => $tmpCompany) {

            /* Check if role is null, meaning all companies */
            if(!$tmpCompany)
                return Ticket::get()->pluck('id')->toArray();


            /* Fetch company */
            $company = Company::withTrashed()->findOrFail($tmpCompany);


            foreach($company->divisions as $division) {
                foreach($division->departments as $department) {
                    foreach($department->employees as $employee) {

                        /* Merge the ticket IDs */
                        if($employee->employee)
                            $ids = array_merge($ids, $employee->employee->tickets()->pluck('id')->toArray());
                    }
                }
            }
        }
        
        return $ids;
    }  

    /**
     * Fetches all ticket IDs handled by the user's role
     */ 
    protected function getIncludedTicketIDs() {

        /* Set necessary data */  
        $user = \Auth::user();
        $tickets = [];


        foreach ($user->groups as $key => $group) {
            
            if($group->hasRole('Updating of Ticket Status') || 
                $group->hasRole('Editing/Removing of Tickets') ||
                $group->hasRole('Generating of Ticketing Reports')) {

                /* If one group has access to all return immediately */
                if($group->type == 0)
                    return Ticket::select(Ticket::MINIMAL_COLUMNS)
                                    ->get()
                                    ->pluck('id')
                                    ->toArray();


                /* Fetch tickets */
                $ids = Ticket::join('forms', 'forms.id', '=', 'tickets.form_id')
                                ->join('form_templates', 'form_templates.id', '=', 'forms.form_template_id')
                                ->select(['tickets.id AS id', 'tickets.form_id AS tickets.form_id', 'forms.id AS forms.id', 'forms.form_template_id AS forms.form_template_id', 'form_templates.id AS form_templates.id', 'form_templates.type AS form_templates.type'])
                                ->where('form_templates.type', ($group->type - 1))
                                ->get()
                                ->pluck('id')
                                ->toArray();

                                // http_response_code(500); dd($ids);


                $tickets = array_merge($tickets, $ids);
            }
        }

        /* Fetch ticket that is assigned to the current user */
        $tickets = array_merge($tickets, $user->getTicketApproval()->pluck('id')->toArray());


        return array_unique($tickets);
    }    

    /**
     * Fetch all company IDs the current user has permission to 
     *
     * @return array
     */
    protected function getCompanyIDs($user, $roles) {
        $companies = [];

        foreach ($user->groups as $key => $group) {
            foreach ($roles as $key => $role) {
                if($group->hasRole($role))
                    array_push($companies, $group->company_id);
            }
        }

        return $companies;
    }  

    /**
     * Calculate percentage
     *
     * @return Int
     */
	public function percent($value, $total) {

		if(!$value)
			return 0;

		return round(($value / $total) * 100, 1, PHP_ROUND_HALF_UP) . '%';
	}	
}
