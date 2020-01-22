<?php

use Illuminate\Database\Seeder;

use Faker\Factory as Faker;
use Carbon\Carbon;

use App\User;
use App\FormTemplate;
use App\FormTemplateCategory;
use App\FormTemplateField;
use App\FormTemplateOption;
use App\FormTemplateApprover;

class SampleFormTemplateTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        echo 'Created form template count:' . "\n";

        for($i = 0; $i <= 20; $i++) {

            $user = User::get()->random()->id;


            $eventCount = FormTemplate::where('form_template_category_id', FormTemplateCategory::EVENT)->count();
            $ldCount = FormTemplate::where('form_template_category_id', FormTemplateCategory::LD)->count();

            $category = !$eventCount ? FormTemplateCategory::EVENT : FormTemplateCategory::FORM;

        	$template = FormTemplate::create([
        		'form_template_category_id' => $category,
        		'name' => ucwords($faker->word) . ' Form',
                'description' => ucfirst($faker->text),
        		'sla' => $faker->numberBetween(3, 5),
        		'sla_option' => 0,
        		'approval_option' => $faker->numberBetween(0, 1),

        		'priority' => $faker->numberBetween(0, 2),
                'type' => $faker->numberBetween(0, 2),
        		
                'creator_id' => $user,
                'updater_id' => $user,

                'created_at' => Carbon::now()->subDays($faker->numberBetween(1, 30)),
                'updated_at' => Carbon::now()->subDays($faker->numberBetween(1, 30)),
        	]);

        	for($o = 0; $o < ($category == FormTemplateCategory::EVENT ? 4 : 6); $o++) {

        		$field = FormTemplateField::create([
        			'form_template_id' => $template->id,
        			'sort' => $o,
        			'label' => ucfirst($faker->word),
        			'type' => $o,
                    'type_value' => $o == 0 ? $faker->numberBetween(0, 9) : null,
                    'isRequired' => $faker->boolean,
        		]);


        		switch ($field['type']) {
        			case 3: case 4:
						
        				for($p = 0; $p < 4; $p++) {

        					$option = FormTemplateOption::create([
        						'form_template_field_id' => $field->id,
        						'sort' => $p,
        						'value' => ucfirst($faker->word),
        					]);
        				}

    				break;
    				case 5:

        				for($l = 0; $l < 3; $l++) {

        					$option = FormTemplateOption::create([
        						'form_template_field_id' => $field->id,
        						'sort' => $l,
        						'value' => ucfirst($faker->word),
        						'type' => $l,
        					]);
        				}

    				break;
        		}
        	}

            for($p = 0; $p < 2; $p++) {

                $approver = FormTemplateApprover::create([
                    'form_template_id' => $template->id,
                    'type' => $p,
                ]);
            }

            echo ($i + 1) . '|';
        }

        echo "\n";


        /* Add to scout index */
        FormTemplate::get()->searchable();        
    }
}
