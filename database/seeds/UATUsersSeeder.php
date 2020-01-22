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

class UATUsersSeeder extends Seeder
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

        $users = [
            [ 'id' => 0, 'first_name' => 'Jonathan', 'last_name' => 'Ching', 'email' => 'jonathan.praxxys@gmail.com' ],
            [ 'id' => 0, 'first_name' => 'Test', 'last_name' => 'Praxxys', 'email' => 'test.praxxys@gmail.com' ],
            [ 'id' => 0, 'first_name' => 'User', 'last_name' => 'Praxxys', 'email' => 'user.praxxys@gmail.com' ],

            /* SNAP Default Users */
            // [ 'id' => 105238, 'first_name' => 'Baby Grace', 'last_name' => 'Umali', 'email' => 'babygrace.umali@snaboitiz.com' ],
            // [ 'id' => 105194, 'first_name' => 'Kitty', 'last_name' => 'Patino', 'email' => 'annachristy.patino@snaboitiz.com' ],
            // [ 'id' => 105163, 'first_name' => 'Candee', 'last_name' => 'Lagura', 'email' => 'candee.lagura@snaboitiz.com' ],
            // [ 'id' => 105251, 'first_name' => 'Landoy', 'last_name' => 'Zamora', 'email' => 'landoy.zamora@snaboitiz.com' ],
            // [ 'id' => 105242, 'first_name' => 'Cynthia', 'last_name' => 'Calixto', 'email' => 'cynthia.calixto@snaboitiz.com' ],
            // [ 'id' => 105247, 'first_name' => 'Rosalyn', 'last_name' => 'White', 'email' => 'rosalyn.white@snaboitiz.com' ],
            // [ 'id' => 105264, 'first_name' => 'Meljun', 'last_name' => 'Canizo', 'email' => 'meljun.canizo@snaboitiz.com' ],
            // [ 'id' => 105130, 'first_name' => 'Cristina', 'last_name' => 'Dianco', 'email' => 'cristina.dianco@snaboitiz.com' ],
            // [ 'id' => 105123, 'first_name' => 'Donald', 'last_name' => 'Deduque', 'email' => 'donald.deduque@snaboitiz.com' ],
            // [ 'id' => 105174, 'first_name' => 'Marjorie', 'last_name' => 'Manipon', 'email' => 'marjorie.manipon@snaboitiz.com' ],
            // [ 'id' => 105132, 'first_name' => 'Bernadeth', 'last_name' => 'Digman', 'email' => 'bernadeth.digman@snaboitiz.com' ],
            // [ 'id' => 105211, 'first_name' => 'Roseanne', 'last_name' => 'Rington', 'email' => 'roseanne.rington@snaboitiz.com' ],
            // [ 'id' => 105095, 'first_name' => 'Ruby Jean', 'last_name' => 'Buenviaje', 'email' => 'rubyjean.buenviaje@snaboitiz.com' ],
            // [ 'id' => 105230, 'first_name' => 'Rosette', 'last_name' => 'Tialengko', 'email' => 'rosette.tialengko@snaboitiz.com' ],
        ];


        echo 'Created user count:' . "\n";


        /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();


        /* Generate random users */
        for ($i = 1; $i <= count($users); $i++) {
            DB::table('users')->insert([
                'id' => $users[$i - 1]['id'] ? $users[$i - 1]['id'] : $i,

                'employee_category_id' => EmployeeCategory::get()->random()->id,
                'location_id' => Location::get()->random()->id,

                'first_name' => $users[$i - 1]['first_name'],
                'middle_name' => '',
                'last_name' => $users[$i - 1]['last_name'],

                'email' => $users[$i - 1]['email'],
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

            echo $i . '|';
        }

        echo "\n";

        /* Assign user supervisor */
        foreach(User::get() as $user) {
            $supervisor = $user->id <= 2 ? User::find($user->id == 2 ? 3 : 2) : User::where('id', '!=', $user->id)->get()->random();

            $user->setSupervisor($supervisor);

            $user->addGroup(Group::find(1));
            $user->addGroup(Group::find(2));
        }


        /*
        | @End Transaction
        |---------------------------------------------*/
        \DB::commit();


        /* Add to scout index */
        User::get()->searchable();
    }
}
