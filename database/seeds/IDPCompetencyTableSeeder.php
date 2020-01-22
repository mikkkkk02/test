<?php

use Illuminate\Database\Seeder;

class IDPCompetencyTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('idp_competencies')->delete();

        $idps = [
        	[ 'name' => 'Electrical Maintenance and Troubleshooting' ],
        	[ 'name' => 'Maintenance Management System' ],
        	[ 'name' => 'Root Cause Analysis' ],
        	[ 'name' => 'Logistics Management' ],
        	[ 'name' => 'Procurement' ],
        	[ 'name' => 'Community Project Development' ],
        	[ 'name' => 'Networking and Partnership Development' ],
        	[ 'name' => 'Community Project Development' ],
        	[ 'name' => 'Stakeholder Management' ],
        	[ 'name' => 'Geoinformatics' ],
        	[ 'name' => 'IT Network Management' ],
        	[ 'name' => 'IT Service Management' ],
        	[ 'name' => 'Systems Performance and Monitoring' ],
        	[ 'name' => 'IT Security' ],
        	[ 'name' => 'Innovation' ],
        	[ 'name' => 'Instrumentation and Control Maintenance Troubleshooting' ],
        	[ 'name' => 'Process Control Parameters' ],
        	[ 'name' => 'Reliability Centered Maintenance' ],
        	[ 'name' => 'Power Plant Protection' ],
        	[ 'name' => 'Building Partnership / Networking' ],
        	[ 'name' => 'Tax Education' ],
        	[ 'name' => 'Tax Accounting and Compliance' ],
        	[ 'name' => 'Accounts Management' ],
        	[ 'name' => 'WESM and AS Opetations' ],
        	[ 'name' => 'Pricing Design' ],
        	[ 'name' => 'Portfolio Analysis' ],
        	[ 'name' => 'Market Intelligence' ],
        	[ 'name' => 'Optimization' ],
        	[ 'name' => 'Water Value' ],
        	[ 'name' => 'Hydroelectric Plant Operations' ],
        	[ 'name' => 'Salesmanship' ],
        	[ 'name' => 'Accounting Standards' ],
        	[ 'name' => 'Revenue Accounting' ],
        	[ 'name' => 'Production Planning' ],
        	[ 'name' => 'Project Planning and Execution' ],
        	[ 'name' => 'Regulatory' ],
        ];

        foreach ($idps as $key => $idp) {
        	\DB::table('idp_competencies')->insert([
        		'id' => $key + 1,
        		'name' => $idp['name'],
        	]);
        }
    }
}
