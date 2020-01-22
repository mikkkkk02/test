<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\User;

class ToggleTestUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:user {--on} {--off}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Enable or disable test users";

    protected $toggle = null;

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
        /* Fetch needed variables */
        $this->fetchOptions();

        /* Run main function */
        $this->main();
    }

    /**
     * Main function
     */
    public function main()
    {
        /* Check toggle command if it exists */
        if($this->toggle != null) {

            /* Run method base on the toggle value */
            switch($this->toggle) {
                case 'off': $this->disable(); break;
                case 'on': $this->enable(); break;
            }

        } else {
            $this->toggle = $this->ask(PHP_EOL . "Enable or disable test user(s)? <fg=yellow;>(E\D)");           

            /* Fetch value of user input */
            switch($this->toggle) {
                case 'd': case 'D': $this->toggle = 'off'; break;
                case 'e': case 'E': $this->toggle = 'on'; break;
                default:
                    $this->info("<fg=red;>Incorrect input!" . PHP_EOL);
                break;
            }

            /* Rerun the main function */
            $this->main();
        }
    }

    /**
     * Enable test users 
     */
    private function enable()
    {
        $this->setStartMessage(1);


        $this->info("<fg=yellow;>Creating: <fg=white;>John doe..." . PHP_EOL);


        $this->setEndMessage(1);
    }

    /**
     * Disable test users 
     */
    private function disable()
    {
        $this->setStartMessage(0);


        $this->info("<fg=yellow;>Archiving: <fg=white;>John doe..." . PHP_EOL);


        $this->setEndMessage(0);        
    }

    /**
     * Fetching of toggle option
     * 
     * @return Boolean
     */
    private function fetchOptions()
    {
        if($this->option('off'))
            $this->toggle = 'off';

        if($this->option('on'))
            $this->toggle = 'on';
    }

    /**
     * Set starting message 
     */
    private function setStartMessage($isEnable = false)
    {
        $this->info(PHP_EOL . ($isEnable ? "Enabling" : "Disabling") ." test users..." . PHP_EOL);
    }

    /**
     * Set starting message 
     */
    private function setEndMessage($isEnable = false)
    {
        $this->info(PHP_EOL . "Test users successfully " . ($isEnable ? "enabled" : "disabled") . "!" . PHP_EOL);
    }    
}
