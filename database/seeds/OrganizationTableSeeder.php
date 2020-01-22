<?php

use Illuminate\Database\Seeder;

use App\Company;

class OrganizationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $companies = [
        	[
                'id' => 1,
                'name' => 'Manila-Oslo Renewable Enterprise, Inc.', 
                'abbreviation' => 'MORE',
                'desc' => 'Manila-Oslo Renewable Enterprise, Inc.'
            ],
        	[
                'id' => 2,
                'name' => 'SN Aboitiz Power-Generation, Inc.', 
                'abbreviation' => 'SNAPG',
                'desc' => 'SN Aboitiz Power-Generation, Inc.'
            ],
        	[
                'id' => 3,
                'name' => 'SN Aboitiz Power-Magat, Inc.', 
                'abbreviation' => 'SNAPM',
                'desc' => 'SN Aboitiz Power-Magat, Inc.'
            ],
        	[
                'id' => 4,
                'name' => 'SN Aboitiz Power-Benguet, Inc.', 
                'abbreviation' => 'SNAPB',
                'desc' => 'SN Aboitiz Power-Benguet, Inc.'
            ],
        ];

        /* Loop and create companies */
        foreach ($companies as $key => $company) {
	        Company::create([
	        	'name' => $company['name'],
	        	'abbreviation' => $company['abbreviation'],
                'description' => $company['desc'],

	        	'creator_id' => 1,
	        	'updater_id' => 1,
	        ]);
        }



        /* Add to scout index */
        Company::get()->searchable();
    }
}
