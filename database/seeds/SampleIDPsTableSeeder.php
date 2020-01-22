<?php

use Illuminate\Database\Seeder;

use Faker\Factory as Faker;

use App\Idp;
use App\IdpCompetency;
use App\User;

class SampleIDPsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('idps')->delete();
 
        $faker = Faker::create();

        echo 'Created IDPs count:' . "\n";

        foreach (User::get() as $key => $user) {

            for($i = 0; $i <= 5; $i++) {

                $status = $faker->numberBetween(0, 2);

                $idp = Idp::create([
                   'employee_id' => $user->id,
                   'competency_id' => IdpCompetency::get()->random()->id,

                   'learning_type' => $faker->numberBetween(0, 2),
                   'competency_type' => $faker->numberBetween(0, 2),

                   'required_proficiency_level' => $faker->numberBetween(1, 5),
                   'current_proficiency_level' => $faker->numberBetween(1, 5),

                   'type' => $faker->numberBetween(0, 3),

                   'details' => $faker->paragraph,

                   'completion_year' => '2017',

                   'status' => $status,

                   'creator_id' => 1,
                   'updater_id' => 1,
                ]);
            }

            echo $key + 1 . '|';
        }

        echo "\n";  


        /* Add to scout index */ 
        User::get()->searchable();              
    }
}
