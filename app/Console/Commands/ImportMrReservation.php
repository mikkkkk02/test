<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\MrReservation;

class ImportMrReservation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:mrreservation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Meeting Room Reservations';


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
        /* Check if env is on debug & local */
        if(!env('APP_DEBUG')) {
            dd("Forbidden: Debugging mode must be set to 'true' to proceed w/ the Permissions Checker");
        }

        /* Check if env is on debug & local */
        $env = env('APP_ENV');
        if(!$env && strtolower($env) != 'local' && strtolower($env) != 'uat') {
            dd("Forbidden: App environment must be set to 'Local' or 'UAT' to proceed w/ the Permissions Checker");
        }

        $this->path = $this->ask("Enter the location of the CSV File to import: <fg=yellow;>");

        if (!file_exists($this->path)) {
            dd('File Doesn\'t exist!');
        }

        $toggle = $this->ask("Proceed with the import of the CSV File for the Meeting Room Reservations? <fg=yellow;>(Y/N)");     

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
        echo "/*----------------------------------------------------- \n";
        echo "| @Importing Meeting Room Reservations! \n";
        echo "|----------------------------------------------------*/ \n";

        $this->import();
    }

    private function import()
    {
        \DB::beginTransaction();

        $row = 0;

        if (($handle = fopen($this->path, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

                if ($row > 0) {
                    $reservation = MrReservation::find($data[0]);

                    if (!$reservation) {
                        $reservation = new MrReservation();
                        $reservation->id = $data[0];
                        $reservation->form_id = $data[1];
                        $reservation->form_template_id = $data[2];
                        $reservation->name = $data[3];
                        $reservation->description = null;
                        $reservation->creator_id = $data[8];
                        $reservation->updater_id = $data[8];
                        $reservation->save();
                        echo "Created #" . $reservation->id . "\r";
                    } else {
                        echo "Updated #" . $reservation->id . "\r";
                    }

                    $reservation->mr_reservation_times()->create([
                        'date' => $data[5],
                        'start_time' => $data[6],
                        'end_time' => $data[7],
                        'creator_id' => $data[8],
                        'updater_id' => $data[8],
                    ]);
                }

                $row++;

            }
            fclose($handle);
        }

        \DB::commit();
    }
}
