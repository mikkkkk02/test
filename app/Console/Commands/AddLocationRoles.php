<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Role;
use App\Group;
use App\Location;

class AddLocationRoles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'location:roles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add in the required location roles & responsibilities';

    protected $location = null;
    protected $time = null;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        /* Check if env is on debug & local */
        if(!env('APP_DEBUG')) {
            dd("Forbidden: Debugging mode must be set to 'true' to proceed w/ the Permissions Checker");
        }

        /* Check if env is on debug & local */
        $env = env('APP_ENV');
        if(!$env && strtolower($env) != 'local' && strtolower($env) != 'uat') {
            dd("Forbidden: App environment must be set to 'Local' or 'UAT' to proceed w/ the Permissions Checker");
        }

        $toggle = $this->ask("Proceed with the adding of location roles & responsibilities? <fg=yellow;>(Y/N)");     

        /* Fetch value of user input */
        switch($toggle) {
            case 'y': case 'Y': $toggle = 1; break;
            case 'n': case 'N': $toggle = 0; break;
            default:
                $this->info("<fg=red;>Incorrect input!" . PHP_EOL);
            break;
        }

        if($toggle == 0) {
            dd('Aborting command!');
        }



        echo "\n";
        echo "/*---------------------------------------------- \n";
        echo "| @Adding Location Roles! \n";
        echo "|----------------------------------------------*/ \n";

        $this->startChecker();
    }

    /**
     * Run the checker.
     *
     * @return void
     */
    public function startChecker()
    {
        $this->info('Commence adding of roles for Locations...');
        $this->init();


        $this->addRoles();
        $this->addRolesToSuperUser();

        
        echo PHP_EOL; echo PHP_EOL;
        $this->info('Operation success!');
        $this->info('<fg=yellow;>Took ' . (time() - $this->time) . ' seconds.');
    }

    /**
     * Set needed global vars
     */
    private function init()
    {
        $this->time = time();
    }

    /**
     * Check Corporation
     *
     * @return void
     */
    private function addRoles()
    {
        echo PHP_EOL;

        $role = (object) [
            'id' => 309, 
            'category_id' => 3, 
            'name' => 'Adding/Editing of Locations', 
            'description' => "",
        ];

        $this->checkRole($role);
    }  

    /**
     * Checker if role exists
     *
     * @return void
     */
    private function checkRole($request)
    {   
        /* Fetch role */
        $this->role = Role::where([
                        'id' => $request->id,
                        'role_category_id' => $request->category_id,
                        'name' => $request->name
                    ])->get()->first();


        /* Check if role already exists */
        if($this->role) {
            $this->info('<fg=yellow;>Role for ' . $request->name . ' already exists...');

            return $this->role;
        }


        $this->info('<fg=white;>Role for ' . $request->name . ' created');

        return Role::create([
                        'id' => $request->id,
                        'role_category_id' => $request->category_id,
                        'name' => $request->name,
                        'description' => $request->description
                    ]);        
    }

    /**
     * Add in roles to the super user
     *
     * @return void
     */
    private function addRolesToSuperUser()
    {
        /* Fetch default super user group */
        $group = Group::find(1);

        /* Show super user details */
        $this->info("\n");        
        $this->info('| <fg=yellow;>Group #' . $group->id);
        $this->info('| -------------------------');
        $this->info('| <fg=green;>Name: <fg=white;>' . $group->name);
        $this->info('| <fg=green;>Company: <fg=white;>' . $group->company);
        $this->info('| <fg=green;>Description: <fg=white;>' . $group->description);

        $sure = $this->ask("\n" . "Is this the default super user group? <fg=yellow;>(Y/N)");

        /* Fetch value of user input */
        switch($sure) {
            case 'y': case 'Y': 

                /* Run adding of requests */
                if(!$group->roles->contains($this->role->id)) {
                    $group->addRole($this->role);
                    $this->info('<fg=white;>Added role ' . $this->role->name . ' to group ' . $group->name . '...');
                } else {
                    $this->info('<fg=yellow;>Role for ' . $this->role->name . ' already added...');
                }

            break;
            case 'n': case 'N': default:
                $this->info("<fg=red;>Cancelling command!" . PHP_EOL);
            break;
        }
    }
}