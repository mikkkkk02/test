<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        'App\Console\Commands\AddMeetingRoomRoles', 
        'App\Console\Commands\AddLocationRoles',
        'App\Console\Commands\SearchableRefresh',
        'App\Console\Commands\ExportMrReservation',
        'App\Console\Commands\ImportMrReservation',
        'App\Console\Commands\NotifyApprovers',
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
        $schedule->command('notify:approvers')
                 ->daily();
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {   
        require base_path('routes/console.php');
    }
}
