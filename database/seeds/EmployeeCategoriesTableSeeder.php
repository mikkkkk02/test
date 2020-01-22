<?php

use Illuminate\Database\Seeder;

class EmployeeCategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('employee_categories')->delete();

        $categories = [
        	['title' => 'Regular'],
        	['title' => 'Intern'],
        ];

        foreach ($categories as $category) {
	        DB::table('employee_categories')->insert([
	        	'title' => $category['title'],
	        ]);
        }
    }
}
