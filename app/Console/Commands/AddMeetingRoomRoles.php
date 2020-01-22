<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Group;
use App\Role;
use App\RoleCategory;

class AddMeetingRoomRoles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'meetingroom:roles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add in the required meeting roles & responsibilities';

    protected $category = null;
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

        $toggle = $this->ask("Proceed with the adding of Meeting Room roles & responsibilities? <fg=yellow;>(Y/N)");     

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
        echo "| @Adding Meeting Room Roles! \n";
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
        $this->info('Commence adding of roles for Meeting Room...');
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

        $category = (object) [
            'id' => 10, 
            'name' => 'Meeting Room Management', 
            'description' => "Roles for managing the meeting room reservations",
            'roles' => [
                [ 'id' => 1000, 'category' => 10, 'name' => 'Viewing of Meeting Room Reservations', 'description' => '' ],
                [ 'id' => 1001, 'category' => 10, 'name' => 'Adding/Editing of Meeting Room Reservations', 'description' => '' ],
                [ 'id' => 1002, 'category' => 10, 'name' => 'Adding/Editing of Meeting Rooms', 'description' => '' ],
                [ 'id' => 1003, 'category' => 10, 'name' => 'Adding/Editing of Meeting Locations', 'description' => '' ],            
            ]
        ];

        /* Fetch category */
        $mrCategory = $this->checkCategory($category);

        /* Add in roles */
        foreach ($category->roles as $key => $role) {
            $this->checkRole($category->id, $role);
        }
    }

    /**
     * Checker if role category exists
     *
     * @return void
     */
    private function checkCategory($request)
    {
        /* Fetch category */
        $this->category = RoleCategory::where([
                                        'id' => $request->id,
                                        'name' => $request->name
                                    ])->get()->first();


        /* Check if category already exists */
        if($this->category)
            return $this->category;

        $this->category = RoleCategory::create([
                                'id' => $request->id, 
                                'name' => $request->name,
                                'description' => $request->name
                            ]);
    }    

    /**
     * Checker if role exists
     *
     * @return void
     */
    private function checkRole($categoryID, $request)
    {
        /* Fetch role */
        $role = Role::where([
                        'id' => $request['id'],
                        'role_category_id' => $categoryID,
                        'name' => $request['name']
                    ])->get()->first();


        /* Check if role already exists */
        if($role) {
            $this->info('<fg=yellow;>Role for ' . $request['name'] . ' already exists...');

            return $role;
        }


        $this->info('<fg=white;>Role for ' . $request['name'] . ' created');

        return Role::create([
                        'id' => $request['id'],
                        'role_category_id' => $categoryID,
                        'name' => $request['name'],
                        'description' => $request['description']
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
                foreach ($this->category->roles as $key => $role) { // dd($role);

                    /* Check if already added */
                    if(!$group->roles->contains($role->id)) {
                        $group->addRole($role);
                        $this->info('<fg=white;>Added role ' . $role->name . ' to group ' . $group->name . '...');
                    } else {
                        $this->info('<fg=yellow;>Role for ' . $role->name . ' already added...');
                    }
                }

            break;
            case 'n': case 'N': default:
                $this->info("<fg=red;>Cancelling command!" . PHP_EOL);
            break;
        }
    }
}