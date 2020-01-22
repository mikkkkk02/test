<?php

use Illuminate\Database\Seeder;

use App\FormTemplateCategory;

class FormTemplateCategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('form_template_categories')->delete();

        $categories = [
        	[ 'id' => FormTemplateCategory::FORM, 'name' => 'Form' ],
        	[ 'id' => FormTemplateCategory::EVENT, 'name' => 'Events' ],
        	[ 'id' => FormTemplateCategory::LD, 'name' => 'L&D' ],
        ];

        foreach($categories as $category) {
	        	
        	DB::table('form_template_categories')->insert([
                'id' => $category['id'],
        		'name' => $category['name'],
        	]);
        }
    }
}
