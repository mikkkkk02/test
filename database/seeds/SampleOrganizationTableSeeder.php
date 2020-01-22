<?php

use Illuminate\Database\Seeder;

use Faker\Factory as Faker;

use App\User;
use App\Location;

use App\Company;
use App\Division;
use App\Department;
use App\Team;

class SampleOrganizationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        /* Group */
        for($g = 10; $g <= 20; $g++) {
			$group = DB::table('divisions')->insert([
				'id' => $g,
				'company_id' => Company::get()->random()->id,
				'name' => $faker->company,
				'description' => $faker->bs,

                'creator_id' => 1,
                'updater_id' => 1,				
			]);
		}

		/* Department & Team */
        for($d = 10; $d <= 20; $d++) {
			$department = DB::table('departments')->insert([
				'id' => $d,
				'division_id' => $d,
				'name' => $faker->company,
				'description' => $faker->bs,

                'creator_id' => 1,
                'updater_id' => 1,				
			]);

			DB::table('teams')->insert([
				'id' => $d,
				'department_id' => $d,
				'name' => $faker->company,
				'description' => $faker->bs,

                'creator_id' => 1,
                'updater_id' => 1,				
			]);	

			DB::table('positions')->insert([
				'id' => $d,
				'department_id' => $d,
				'title' => $faker->jobTitle,
				'description' => $faker->bs,
				
                'creator_id' => 1,
                'updater_id' => 1,
			]);						
		}


        /* Add to scout index */
        Division::get()->searchable();
        Department::get()->searchable();
        Team::get()->searchable();
        Position::get()->searchable();
    }
}
