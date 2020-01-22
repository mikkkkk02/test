<?php

use Illuminate\Database\Seeder;

use Faker\Factory as Faker;

use App\Group;

use App\User;
use App\Location;
use App\Department;
use App\Team;
use App\Position;
use App\EmployeeCategory;

class SampleUserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('assignee_user')->delete();
        DB::table('users')->delete();

        $faker = Faker::create();

        echo 'Created user count:' . "\n";

        /* Generate random users */
        for ($i = 1; $i <= 50; $i++) {
            DB::table('users')->insert([
                'id' => $i,

                'employee_category_id' => EmployeeCategory::get()->random()->id,
                'location_id' => Location::get()->random()->id,

                'first_name' => $faker->firstName,
                'middle_name' => '',
                'last_name' => $faker->firstName,

                'email' => $faker->userName . '@gmail.com',
                'password' => Hash::make('password'),

                'contact_no' => $faker->e164PhoneNumber,
                'company_no' => $faker->e164PhoneNumber,

                'address_line1' => $faker->address,
                'address_line2' => $faker->address,

                'job_level' => 'Sample Job level',
                'job_grade' => 'Sample Job Grade',
                'cost_center' => strtoupper($faker->randomLetter . $faker->randomLetter . $faker->randomLetter . $faker->randomNumber(3)),

                'status' => 1,
                'verify_token' => bin2hex(random_bytes(64)),

                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            $this->assignUser($i);

            echo $i . '|';
        }

        echo "\n";

        /* Assign user supervisor */
        foreach(User::get() as $user) {
            $supervisor = User::where('id', '!=', $user->id)->get()->random();

            $user->setSupervisor($supervisor);
            $user->addGroup(Group::find(2));
        }


        /* Add to scout index */
        User::get()->searchable();        
    }

    public function assignUser($user) {

        $department = Department::get()->random(); // dd($department->id);
        $team = Team::where('department_id', $department->id)->first(); // dd($team->id);
        $position = Position::where('department_id', $department->id)->first(); // dd($team->id);

        User::find($user)->assignDepartment($department->id, $position->id, $team->id);
    }
}
