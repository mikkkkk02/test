<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        echo "\n";
        echo "/*---------------------------------------------- \n";
        echo "| @Reset Data! \n";
        echo "|----------------------------------------------*/ \n";

        $this->reset();


        echo "\n";
        echo "/*---------------------------------------------- \n";
        echo "| @Populating Data! \n";
        echo "|----------------------------------------------*/ \n";

        /*
        |-----------------------------------------------
        | @Roles
        |----------------------------------------------*/       
        $this->call(RolesTableSeeder::class);

        /*
        |-----------------------------------------------
        | @Groups
        |----------------------------------------------*/       
        $this->call(GroupsTableSeeder::class);

        /*
        |-----------------------------------------------
        | @Locations
        |----------------------------------------------*/       
        $this->call(LocationsTableSeeder::class);

        /*
        |-----------------------------------------------
        | @Organization
        |----------------------------------------------*/  
        $this->call(OrganizationTableSeeder::class);

	    /*
	    |-----------------------------------------------
	    | @Employee Categories
	    |----------------------------------------------*/    	
        $this->call(EmployeeCategoriesTableSeeder::class);

        /*
        |-----------------------------------------------
        | @Form Template Categories
        |----------------------------------------------*/       
        $this->call(FormTemplateCategoriesTableSeeder::class);

        /*
        |-----------------------------------------------
        | @Event Categories
        |----------------------------------------------*/       
        $this->call(EventCategoriesTableSeeder::class);

        /*
        |-----------------------------------------------
        | @IDP Competency
        |----------------------------------------------*/       
        $this->call(IDPCompetencyTableSeeder::class);

        /*
        |-----------------------------------------------
        | @Default Forms
        |----------------------------------------------*/       
        // $this->call(FormsSeeder::class);

        /*
        |-----------------------------------------------
        | @Default Settings
        |----------------------------------------------*/       
        // $this->call(SettingsSeeder::class); 

        
        echo "\n" . "Uncomment me to proceed to the sample data seeders" . "\n"; exit();

        echo "\n";
        echo "/*---------------------------------------------- \n";
        echo "| @Populating Sample Data! \n";
        echo "|----------------------------------------------*/ \n";

        /*
        |-----------------------------------------------
        | @Default Users
        |----------------------------------------------*/       
        // $this->call(UATUsersSeeder::class);

        /*
        | @Organization
        |----------------------------------------------*/        
        // $this->call(SampleOrganizationTableSeeder::class);

        /*
        | @Employees
        |----------------------------------------------*/
        // $this->call(SampleUserTableSeeder::class);

        /*
        |-----------------------------------------------
        | @Settings
        |----------------------------------------------*/       
        // $this->call(SampleSettingsTableSeeder::class);

        /*
        | @Form Templates
        |----------------------------------------------*/
        // $this->call(SampleFormTemplateTableSeeder::class);

        /*
        | @Events
        |----------------------------------------------*/        
        // $this->call(SampleEventsTableSeeder::class);

        /*
        | @IDP
        |----------------------------------------------*/        
        // $this->call(SampleIDPsTableSeeder::class);  

        /*
        | @Requests
        |----------------------------------------------*/        
        // $this->call(SampleRequestsTableSeeder::class); 
    }

    public function reset() {

        echo "Forms \n";
        $this->resetForms();
        
        echo "Organization \n";
        $this->resetOrganization();
    }

    public function resetForms() {
        DB::table('forms')->delete();
        DB::table('form_approvers')->delete();
        DB::table('form_updates')->delete();
        DB::table('form_templates')->delete();
        DB::table('form_template_fields')->delete();
        DB::table('form_template_options')->delete();
        DB::table('form_template_approvers')->delete();
    }

    public function resetOrganization() {

        DB::table('companies')->delete();
        DB::table('divisions')->delete();
        DB::table('departments')->delete();
        DB::table('teams')->delete();        
        DB::table('positions')->delete();        
    }
}
