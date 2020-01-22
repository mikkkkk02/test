<?php

use Illuminate\Database\Seeder;

class EventCategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('event_categories')->delete();

        $categories = [
        	[ 'title' => 'Category 1' ],
        	[ 'title' => 'Category 2' ],
        	[ 'title' => 'Category 3' ],
        ];

        foreach ($categories as $category) {
	        	
        	DB::table('event_categories')->insert([
        		'title' => $category['title'],
        	]);
        }
    }
}
