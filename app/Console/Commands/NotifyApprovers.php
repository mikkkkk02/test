<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Notifications\Requests\RequestIsPending;
use App\User;
use App\Form;
use App\FormApprover;

use Carbon\Carbon;

class NotifyApprovers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify:approvers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notify Approvers for Pending Requests';

    /**
     * File Path of the CSV
     * @var string
     */
    private $path;

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
        //*******__get all Requests that is 7days old...
        $form = [];
        $approvers = [];

        $form = Form::where('status' , 0 )
                    ->whereDate('created_at', '<', Carbon::now()->subDays(7))
                    ->get();

        foreach ($form as $reqform) {
            $formId = $reqform->id;

            //*******__get all ID of Approvers for each Requests
            $approvers = FormApprover::where('form_id', $formId)->get();

            foreach ($approvers as $formApprover) {
                if($formApprover->enabled == 1){
                    $approver_id = $formApprover->approver_id;

                    //********__Notify the Approver
                    $user = User::find($approver_id);
                    $user->notify(new RequestIsPending($reqform, $formApprover));

                    echo $formId."\n";
                    echo "\t".$approver_id."\n";   
                    echo "\t".$user->first_name." ".$user->last_name."\n";
                }
            }
        }
    }
}
