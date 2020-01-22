<?php

use Illuminate\Database\Seeder;

use App\Role;
use App\Group;

class GroupsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('groups')->delete();

        $groups = [
            [
                'name' => 'Super User', 'description' => '', 'roles' => 
                    [
                        100,101,
                        200,
                        300,301,302,303,304,305,306,307,308,
                        400,401,402,403,404,
                        500,501,502,503,504,
                        600,601,602,603,604,605,
                        700,701,702,703,704,705,706,
                        800,801,
                        900,
                    ],
                'type' => 0,
                'company' => null ],

            [
                'name' => 'Employee Self Service', 'description' => '', 'roles' => 
                    [
                        100,101
                    ],
                'type' => 0,
                'company' => null ],

        	[
                'name' => 'MORE Super User', 'description' => '', 'roles' => 
                    [
                        100,101,
                        200,
                        300,302,303,304,305,306,307,308,
                        400,401,402,403,404,
                        500,501,502,503,504,
                        600,601,602,603,604,605,
                        700,701,702,703,704,705,706
                    ],
                'type' => 0,
                'company' => 1 ],
        	[
                'name' => 'SNAPG Super User', 'description' => '', 'roles' => 
                    [
                        100,101,
                        200,
                        300,302,303,304,305,306,307,308,
                        400,401,402,403,404,
                        500,501,502,503,504,
                        600,601,602,603,604,605,
                        700,701,702,703,704,705,706
                    ],
                'type' => 0,
                'company' => 2 ],
        	[
                'name' => 'SNAPM Super User', 'description' => '', 'roles' => 
                    [
                        100,101,
                        200,
                        300,302,303,304,305,306,307,308,
                        400,401,402,403,404,
                        500,501,502,503,504,
                        600,601,602,603,604,605,
                        700,701,702,703,704,705,706
                    ],
                'type' => 0,
                'company' => 3 ],
            [
                'name' => 'SNAPB Super User', 'description' => '', 'roles' => 
                    [
                        100,101,
                        200,
                        300,302,303,304,305,306,307,308,
                        400,401,402,403,404,
                        500,501,502,503,504,
                        600,601,602,603,604,605,
                        700,701,702,703,704,705,706
                    ],
                'type' => 0,
                'company' => 4 ],

        	[
                'name' => 'Admin Super User', 'description' => '', 'roles' => 
                    [
                        100,101,
                        400,401,402,403,404,
                        600,601,602,603,604,
                        700,701,702,703,704,705,706
                    ],
                'type' => 1,
                'company' => null ],
        	[
                'name' => 'Admin User', 'description' => '', 'roles' => 
                    [
                        100,101,
                        401,402,403,
                        600,604,
                        700,702,703,704,705,706
                    ],
                'type' => 1,
                'company' => null ],
        	[
                'name' => 'MORE Admin Super User', 'description' => '', 'roles' => 
                    [
                        100,101,
                        400,401,402,403,
                        600,601,602,603,604,
                        700,701,702,703,704,705,706
                    ],
                'type' => 1,
                'company' => 1 ],
        	[
                'name' => 'MORE Admin User', 'description' => '', 'roles' => 
                    [
                        100,101,
                        401,402,403,
                        604,
                        700,703,704
                    ],
                'type' => 1,
                'company' => 1 ],
        	[
                'name' => 'SNAPG Admin Super User', 'description' => '', 'roles' => 
                    [
                        100,101,
                        400,401,402,403,
                        600,601,602,603,604,
                        700,701,702,703,704,705,706
                    ],
                'type' => 1,
                'company' => 2 ],
        	[
                'name' => 'SNAPG Admin User', 'description' => '', 'roles' => 
                    [
                        100,101,
                        401,402,403,
                        604,
                        700,703,704
                    ],
                'type' => 1,
                'company' => 2 ],
        	[
                'name' => 'SNAPM Admin Super User', 'description' => '', 'roles' => 
                    [
                        100,101,
                        400,401,402,403,
                        600,601,602,603,604,
                        700,701,702,703,704,705,706
                    ],
                'type' => 1,
                'company' => 3 ],
        	[
                'name' => 'SNAPM Admin User', 'description' => '', 'roles' => 
                    [
                        100,101,
                        401,402,403,
                        604,
                        700,703,704
                    ],
                'type' => 1,
                'company' => 3 ],
            [
                'name' => 'SNAPB Admin Super User', 'description' => '', 'roles' => 
                    [
                        100,101,
                        400,401,402,403,
                        600,601,602,603,604,
                        700,701,702,703,704,705,706
                    ],
                'type' => 1,
                'company' => 4 ],
            [
                'name' => 'SNAPB Admin User', 'description' => '', 'roles' => 
                    [
                        100,101,
                        401,402,403,
                        604,
                        700,703,704
                    ],
                'type' => 1,
                'company' => 4 ],

        	[
                'name' => 'HR Super User', 'description' => '', 'roles' => 
                    [
                        100,101,
                        200,
                        300,301,302,303,304,305,306,307,308,
                        400,401,402,403,
                        600,601,602,603,605,
                        700,701,702,703,704,705,706
                    ],
                'type' => 2,
                'company' => null ],
        	[
                'name' => 'HR User', 'description' => '', 'roles' => 
                    [
                        100,101,
                        200,
                        300,302,303,304,305,306,307,308,
                        401,402,403,
                        600,605,
                        700,702,703,704,705,706
                    ],
                'type' => 2,
                'company' => null ],
        	[
                'name' => 'MORE HR Super User', 'description' => '', 'roles' => 
                    [
                        100,101,
                        200,
                        300,302,303,304,305,306,307,308,
                        400,401,402,403,
                        600,601,602,603,605,
                        700,701,702,703,704,705,706
                    ],
                'type' => 2,
                'company' => 1 ],
        	[
                'name' => 'MORE HR User', 'description' => '', 'roles' => 
                    [
                        100,101,
                        300,302,303,304,305,306,307,
                        401,402,403,
                        605,
                        700,703,704
                    ],
                'type' => 2,
                'company' => 1 ],
        	[
                'name' => 'SNAPG HR Super User', 'description' => '', 'roles' => 
                    [
                        100,101,
                        200,
                        300,302,303,304,305,306,307,308,
                        400,401,402,403,
                        600,601,602,603,605,
                        700,701,702,703,704,705,706
                    ],
                'type' => 2,
                'company' => 2 ],
        	[
                'name' => 'SNAPG HR User', 'description' => '', 'roles' => 
                    [
                        100,101,
                        300,302,303,304,305,306,307,
                        401,402,403,
                        605,
                        700,703,704
                    ],
                'type' => 2,
                'company' => 2 ],
        	[
                'name' => 'SNAPM HR Super User', 'description' => '', 'roles' => 
                    [
                        100,101,
                        200,
                        300,302,303,304,305,306,307,308,
                        400,401,402,403,
                        600,601,602,603,605,
                        700,701,702,703,704,705,706
                    ],
                'type' => 2,
                'company' => 3 ],
        	[
                'name' => 'SNAPM HR User', 'description' => '', 'roles' => 
                    [
                        100,101,
                        300,302,303,304,305,306,307,
                        401,402,403,
                        605,
                        700,703,704
                    ],
                'type' => 2,
                'company' => 3 ],
            [
                'name' => 'SNAPB HR Super User', 'description' => '', 'roles' => 
                    [
                        100,101,
                        200,
                        300,302,303,304,305,306,307,308,
                        400,401,402,403,
                        600,601,602,603,605,
                        700,701,702,703,704,705,706
                    ],
                'type' => 2,
                'company' => 4 ],
            [
                'name' => 'SNAPB HR User', 'description' => '', 'roles' => 
                    [
                        100,101,
                        300,302,303,304,305,306,307,
                        401,402,403,
                        605,
                        700,703,704
                    ],
                'type' => 3,
                'company' => 4 ],

        	[
                'name' => 'OD Super User', 'description' => '', 'roles' => 
                    [
                        100,101,
                        400,401,402,403,404,
                        500,501,502,503,504,
                        600,601,602,603,
                        700,701,702,703,704,705,706
                    ],
                'type' => 3,
                'company' => null ],
        	[
                'name' => 'OD User', 'description' => '', 'roles' => 
                    [
                        100,101,
                        401,402,403,404,
                        501,502,503,504,
                        600,
                        700,702,703,704,705,706
                    ],
                'type' => 3,
                'company' => null ],
        	[
                'name' => 'MORE OD Super User', 'description' => '', 'roles' => 
                    [
                        100,101,
                        400,401,402,403,404,
                        500,501,502,503,504,
                        600,601,602,603,
                        700,701,702,703,704,705,706
                    ],
                'type' => 3,
                'company' => 1 ],
        	[
                'name' => 'MORE OD User', 'description' => '', 'roles' => 
                    [
                        100,101,
                        401,402,403,404,
                        501,502,503,504,
                        700,703,704
                    ],
                'type' => 3,
                'company' => 1 ],
        	[
                'name' => 'SNAPG OD Super User', 'description' => '', 'roles' => 
                    [
                        100,101,
                        400,401,402,403,404,
                        500,501,502,503,504,
                        600,601,602,603,
                        700,701,702,703,704,705,706
                    ],
                'type' => 3,
                'company' => 2 ],
        	[
                'name' => 'SNAPG OD User', 'description' => '', 'roles' => 
                    [
                        100,101,
                        401,402,403,404,
                        501,502,503,504,
                        700,703,704
                    ],
                'type' => 3,
                'company' => 2 ],
        	[
                'name' => 'SNAPM OD Super User', 'description' => '', 'roles' => 
                    [
                        100,101,
                        400,401,402,403,404,
                        500,501,502,503,504,
                        600,601,602,603,
                        700,701,702,703,704,705,706
                    ],
                'type' => 3,
                'company' => 3 ],
        	[
                'name' => 'SNAPM OD User', 'description' => '', 'roles' => 
                    [
                        100,101,
                        401,402,403,404,
                        501,502,503,504,
                        700,703,704
                    ],
                'type' => 3,
                'company' => 3 ],
            [
                'name' => 'SNAPB OD Super User', 'description' => '', 'roles' => 
                    [
                        100,101,
                        400,401,402,403,404,
                        500,501,502,503,504,
                        600,601,602,603,
                        700,701,702,703,704,705,706
                    ],
                'type' => 3,
                'company' => 4 ],
            [
                'name' => 'SNAPB OD User', 'description' => '', 'roles' => 
                    [
                        100,101,
                        401,402,403,404,
                        501,502,503,504,
                        700,703,704
                    ],
                'type' => 3,
                'company' => 4 ],
        ];


        /* Create responsibilities */
        foreach ($groups as $key => $group) {
	        $groupObj = Group::create([
                'id' => $key + 1,

                'company_id' => $group['company'],

                'type' => $group['type'],

	        	'name' => $group['name'],
	        	'description' => $group['description'],
	        ]);

            foreach ($group['roles'] as $role) {
                $role = Role::findOrFail($role);

                $groupObj->addRole($role);
            }
        }


        /* Add to scout index */
        Group::get()->searchable();
    }
}
