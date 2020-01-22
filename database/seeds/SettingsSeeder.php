<?php

use Illuminate\Database\Seeder;

use App\User;
use App\Company;
use App\Settings;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();    	

        $this->setSettings();
        $this->setTechnicians();


        /*
        | @End Transaction
        |---------------------------------------------*/
        \DB::commit();        
    }


    public function setSettings() 
    {
        /* Fetch or create */
        $settings = Settings::get()->count() > 0 ? Settings::get()->first() : Settings::create();

        /* Set the default settings */
        // $settings->ceo_id = 0;
        // $settings->hr_id = 0;
        // $settings->od_id = 0;

        $settings->save();
    }

    /**
     * Set default technicians per company
     *
     * @return void
     */    
    public function setTechnicians()
    {
        $companies = [
            [ 'name' => 'SNAPB', 'types' => 
            	[
            		[ 'type' => 'HR', 'technician' => 'marjorie.manipon@snaboitiz.com' ],
            		[ 'type' => 'Admin', 'technician' => 'bernadeth.digman@snaboitiz.com' ],
            		[ 'type' => 'OD', 'technician' => 'candee.lagura@snaboitiz.com' ],
            	]
            ],
            [ 'name' => 'MORE', 'types' => 
            	[
            		[ 'type' => 'HR', 'technician' => 'roseanne.rington@snaboitiz.com' ],
            		[ 'type' => 'Admin', 'technician' => 'cristina.dianco@snaboitiz.com' ],
            		[ 'type' => 'OD', 'technician' => 'candee.lagura@snaboitiz.com' ],
            	]
            ],
            [ 'name' => 'SNAPM',  'types' => 
            	[
            		[ 'type' => 'HR', 'technician' => 'cynthia.calixto@snaboitiz.com' ],
            		[ 'type' => 'Admin', 'technician' => 'cynthia.calixto@snaboitiz.com' ],
            		[ 'type' => 'OD', 'technician' => 'candee.lagura@snaboitiz.com' ],
            	]
            ],
            [ 'name' => 'SNAPG',  'types' => 
            	[
            		[ 'type' => 'HR', 'technician' => 'roseanne.rington@snaboitiz.com' ],
            		[ 'type' => 'Admin', 'technician' => 'cristina.dianco@snaboitiz.com' ],
            		[ 'type' => 'OD', 'technician' => 'candee.lagura@snaboitiz.com' ],
            	]
            ]
        ];

        /* Loop company data array */
        foreach($companies as $c) {

        	$company = $this->getCompany($c['name']);

        	/* Check if company exists */
        	if($company) {

	        	/* Loop type */
	        	foreach($c['types'] as $key => $t) {

	        		$technician = $this->getEmployee($t['technician']);

	        		/* Check if technician exists */
	        		if($technician && $technician->exists()) {

		        		/* Set per type */
		        		switch($t['type']) {
		        			case 'HR': $company->hr_technician_id = $technician->id; break;
		        			case 'Admin': $company->admin_technician_id = $technician->id; break;
		        			case 'OD': $company->od_technician_id = $technician->id; break;
		        		}

		        		$company->save();
	        		}
	        	}
        	}
    	}	
    }

    /**
     * Get employee by ID or email
     *
     * @return void
     */ 
    public function getEmployee($string) 
    {
        return User::where('id', $string)
                    ->orWhere('email', $string)
                    ->get()
                    ->first();
    }

    /**
     * Get company by abbreviation or name
     *
     * @return void
     */ 
    public function getCompany($string) 
    {
        return Company::where('abbreviation', $string)
                    ->orWhere('name', $string)
                    ->get()
                    ->first();
    }        
}
