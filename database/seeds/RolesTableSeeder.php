<?php

use Illuminate\Database\Seeder;

use Faker\Factory as Faker;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        DB::table('role_categories')->delete();
        DB::table('roles')->delete();


        $categories = [
            [ 'id' => 1, 'name' => 'Employee Self Service', 'description' => "Roles for managing the employees capability to update & submit forms" ],
            [ 'id' => 2, 'name' => 'User Management', 'description' => "Roles for managing the users capability to update user groups & responsibilities" ],
            [ 'id' => 3, 'name' => 'Employee & Department Management', 'description' => "Roles for managing the employee, company, group/division, department & positions" ],
            [ 'id' => 4, 'name' => 'Calendar Management', 'description' => "Roles for managing the company events" ],
            [ 'id' => 5, 'name' => 'IDP Management', 'description' => "Roles for managing the employee's learning activities" ],
            [ 'id' => 6, 'name' => 'Forms Management', 'description' => "Roles for managing the company forms" ],
            [ 'id' => 7, 'name' => 'Ticketing Management', 'description' => "Roles for managing tickets" ],
            [ 'id' => 8, 'name' => 'Settings Management', 'description' => "Roles for managing the general settings" ],
            [ 'id' => 9, 'name' => 'Government Forms Management', 'description' => "Roles for managing the government forms" ],
            [ 'id' => 10, 'name' => 'Meeting Room Management', 'description' => "Roles for managing the meeting room reservations" ],
        ];

        foreach ($categories as $key => $category) {
            DB::table('role_categories')->insert([
                'id' => $key + 1,
                'name' => $category['name'],
                'description' => $category['description'],
            ]);
        }

        $roles = [

            /* Employee Self Service */
        	[ 'id' => 100, 'category' => 1, 'name' => 'Updating of Profile', 'description' => $faker->text(150) ],
        	[ 'id' => 101, 'category' => 1, 'name' => 'Submission of Forms', 'description' => $faker->text(150) ],

            /* User Management */
        	[ 'id' => 200, 'category' => 2, 'name' => 'Adding/Editing of User Responsibilities/Groups', 'description' => $faker->text(150) ],

            /* Employee & Department Management */
        	[ 'id' => 300, 'category' => 3, 'name' => 'Batch updating of Employee Database', 'description' => $faker->text(150) ],
            [ 'id' => 301, 'category' => 3, 'name' => 'Adding/Editing of Company', 'description' => $faker->text(150) ],
            [ 'id' => 302, 'category' => 3, 'name' => 'Adding/Editing of Group', 'description' => $faker->text(150) ],
        	[ 'id' => 303, 'category' => 3, 'name' => 'Adding/Editing of Department', 'description' => $faker->text(150) ],
        	[ 'id' => 304, 'category' => 3, 'name' => 'Adding/Editing of Positions', 'description' => $faker->text(150) ],
            [ 'id' => 305, 'category' => 3, 'name' => 'Adding/Editing of Teams', 'description' => $faker->text(150) ],
        	[ 'id' => 306, 'category' => 3, 'name' => 'Assigning/Removing positions of Employee', 'description' => $faker->text(150) ],
        	[ 'id' => 307, 'category' => 3, 'name' => 'Adding/Editing of Employee Profile', 'description' => $faker->text(150) ],
        	[ 'id' => 308, 'category' => 3, 'name' => 'Adding/Editing of Employee User Responsibilities/Group', 'description' => $faker->text(150) ],
            [ 'id' => 309, 'category' => 3, 'name' => 'Adding/Editing of Locations', 'description' => $faker->text(150) ],

            /* Calendar Management */
        	[ 'id' => 400, 'category' => 4, 'name' => 'Batch Uploading of Events', 'description' => $faker->text(150) ],
        	[ 'id' => 401, 'category' => 4, 'name' => 'Adding/Editing of Events', 'description' => $faker->text(150) ],
        	[ 'id' => 402, 'category' => 4, 'name' => 'Add/Remove Participants to Event', 'description' => $faker->text(150) ],
        	[ 'id' => 403, 'category' => 4, 'name' => 'Confirm Attendance of Participants', 'description' => $faker->text(150) ],
            [ 'id' => 404, 'category' => 4, 'name' => 'Generating of BBLS Reports', 'description' => $faker->text(150) ],

            /* IDP Management */
        	[ 'id' => 500, 'category' => 5, 'name' => 'Batch Uploading of Learning Activities', 'description' => $faker->text(150) ],
        	[ 'id' => 501, 'category' => 5, 'name' => 'Adding/Editing of Learning Activities', 'description' => $faker->text(150) ],
        	[ 'id' => 502, 'category' => 5, 'name' => 'Assigning/Reassigning/Removing of Learning Activities to Employees', 'description' => $faker->text(150) ],
        	[ 'id' => 503, 'category' => 5, 'name' => 'Confirm Attendance of Participants', 'description' => $faker->text(150) ],
            [ 'id' => 504, 'category' => 5, 'name' => 'Generating of L&D Reports', 'description' => $faker->text(150) ],

            /* Forms Management */
        	[ 'id' => 600, 'category' => 6, 'name' => 'Creating/Designing/Editing/Removing of Forms', 'description' => $faker->text(150) ],
        	[ 'id' => 601, 'category' => 6, 'name' => 'Adding/Editing of Assignees', 'description' => $faker->text(150) ],
        	[ 'id' => 602, 'category' => 6, 'name' => 'Creating/Editing/Removing of hierarchy and workflow', 'description' => $faker->text(150) ],
        	[ 'id' => 603, 'category' => 6, 'name' => 'Workflow Management of Forms', 'description' => $faker->text(150) ],
            [ 'id' => 604, 'category' => 6, 'name' => 'Generating of Admin Reports', 'description' => $faker->text(150) ],
            [ 'id' => 605, 'category' => 6, 'name' => 'Generating of HR Reports', 'description' => $faker->text(150) ],

            /* Ticketing Management */
        	[ 'id' => 700, 'category' => 7, 'name' => 'Creating of Tickets', 'description' => $faker->text(150) ],
        	[ 'id' => 701, 'category' => 7, 'name' => 'Editing/Removing of Tickets', 'description' => $faker->text(150) ],
        	[ 'id' => 702, 'category' => 7, 'name' => 'Assigning of Tickets', 'description' => $faker->text(150) ],
        	[ 'id' => 703, 'category' => 7, 'name' => 'Pushing of Tickets', 'description' => $faker->text(150) ],
        	[ 'id' => 704, 'category' => 7, 'name' => 'Updating of Ticket Status', 'description' => $faker->text(150) ],
        	[ 'id' => 705, 'category' => 7, 'name' => 'Generating of Ticketing Reports', 'description' => $faker->text(150) ],
        	[ 'id' => 706, 'category' => 7, 'name' => 'Workflow Management of Tickets', 'description' => $faker->text(150) ],

            /* Settings Management */
            [ 'id' => 800, 'category' => 8, 'name' => 'Editing of CEO, HR & OD', 'description' => $faker->text(150) ],
            [ 'id' => 801, 'category' => 8, 'name' => 'Editing of Ticket Technician', 'description' => $faker->text(150) ],

            /* Government Forms Management */
            [ 'id' => 900, 'category' => 9, 'name' => 'Adding/Editing of Government Forms', 'description' => $faker->text(150) ],

            [ 'id' => 1000, 'category' => 10, 'name' => 'Viewing of Meeting Room Reservations', 'description' => $faker->text(150) ],
            [ 'id' => 1001, 'category' => 10, 'name' => 'Adding/Editing of Meeting Room Reservations', 'description' => $faker->text(150) ],
            [ 'id' => 1002, 'category' => 10, 'name' => 'Adding/Editing of Meeting Rooms', 'description' => $faker->text(150) ],
            [ 'id' => 1003, 'category' => 10, 'name' => 'Adding/Editing of Meeting Locations', 'description' => $faker->text(150) ],            
        ];

        foreach ($roles as $key => $role) {
        	DB::table('roles')->insert([
        		'id' => $role['id'],
                'role_category_id' => $role['category'],
        		'name' => $role['name'],
                'description' => $role['description'],
        	]);
        }
    }
}
