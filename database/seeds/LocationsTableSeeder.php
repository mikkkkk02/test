<?php

use Illuminate\Database\Seeder;

class LocationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('locations')->delete();

        $locations = [
        	['id' => 1, 'name' => 'Manila'],
        	['id' => 2, 'name' => 'Taguig'],
        	['id' => 3, 'name' => 'Ambuklao'],
        	['id' => 4, 'name' => 'Magat'],
        	['id' => 5, 'name' => 'Binga'],
        ];

        foreach($locations as $location) {
        	DB::table('locations')->insert([
                'id' => $location['id'],
        		'name' => $location['name'],
        	]);
        }
    }
}
