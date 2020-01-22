<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Excel;
use Carbon\Carbon;

use App\FormTemplate;
use App\Form;
use App\FormAnswer;

class ExportMrReservation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'export:mrreservation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export Meeting Room Reservation Form and Answers';

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

        $toggle = $this->ask("Proceed with the export of Meeting Room Reservation Form and Answers? <fg=yellow;>(Y/N)");     

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
        echo "| @Exporting Meeting Room Reservation! \n";
        echo "|----------------------------------------------------*/ \n";

        $this->export();
    }

    /**
     * Export a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    private function export()
    {
        $dt = Carbon::now();

        return Excel::create('mr_migrations ' . $dt->format('ymd-His'), function($excel) use ($dt) {

            $excel->sheet('requests', function($sheet) {

                $data = [];
                $requests = [];
                $ids = [];

                $formIDs = Form::where('form_template_id', 14)->pluck('id')->toArray();
                $formAnswers = FormAnswer::whereIn('form_id', $formIDs)->
                        where('form_template_field_id', 415)->
                        get();


                /* Create the excel columns */
                foreach($formAnswers as $key => $answer) {
                    $answers = json_decode($answer->value, true);

                    $date = $this->getDate($answer->form->mr_date);
                    $startTime = $this->getTime($answer->form->mr_start_time);
                    $endTime = $this->getTime($answer->form->mr_end_time);

                    if ($this->validateDates($date, $startTime, $endTime)) {
                        $data[$key] = [
                            'id' => $answer->form_id,
                            'form_id' => $answer->form_id,
                            'form_template_id' => 14,
                            'title' => $answer->form->mr_title,
                            'status' => 'Parent',
                            'date' => $date,
                            'start_time' => $startTime,
                            'end_time' => $endTime,
                            'owner' => $answer->form->employee ? $answer->form->employee->id : 1,
                        ];
                    }

                    foreach ($answers as $index => $value) {
                        $array = array_values($value);

                        if (!in_array('', $array)) {
                            $date = $this->getDate($array[0]);
                            $startTime = $this->getTime($array[1]);
                            $endTime = $this->getTime($array[2]);

                            if ($this->validateDates($date, $startTime, $endTime)) {
                                echo "Invalid format on " . $answer->id . "\n";
                                echo $date . "\n";
                                echo $startTime . "\n";
                                echo $endTime . "\n\n";
                                array_push($ids, $answer->id);
                                $status = 'Error';
                            } else {
                                $status = 'Success';
                            }

                            $data[$key . $index] = [
                                'id' => $answer->id,
                                'form_id' => $answer->form_id,
                                'form_template_id' => 14,
                                'title' => $answer->form->mr_title,
                                'status' => $status,
                                'date' => $date,
                                'start_time' => $startTime,
                                'end_time' => $endTime,
                                'owner' => $answer->form->employee ? $answer->form->employee->id : 1,
                            ];
                        }

                    }
                }

                $sheet->fromArray($data);

            });

        })->store('xlsx');
    }

    private function getDate($setDate) {
        $date = date_parse($setDate);

        if (!$date['year']) {
            $date['year'] = 2018;
        }

        if (!$date['error_count'] && $date['month'] && $date['day']) {

            $date = Carbon::createFromDate($date['year'], $date['month'], $date['day']);
            $date = $date->toDateString();

        } else {
            echo $setDate . "\n";
            $date = $setDate;
        }

        return $date;
    }

    private function getTime($setTime) {
        $time = date_parse($setTime);

        if (!$time['error_count'] && 
            $time['hour'] !== false && 
            $time['minute'] !== false && 
            $time['second'] !== false
        ) {
            $time = Carbon::createFromTime($time['hour'], $time['minute'], $time['second']);
            $time = $time->toTimeString();
        } else {
            $time = $setTime;
        }

        return $time;
    }

    private function validateDates($date, $startTime, $endTime) {
        return (!strtotime($date) && $date != null) ||
                (!strtotime($startTime) && $startTime != null) ||
                (!strtotime($endTime) && $endTime != null);
    }
}
