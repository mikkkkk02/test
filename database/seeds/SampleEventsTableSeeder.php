<?php

use Illuminate\Database\Seeder;

use Faker\Factory as Faker;
use Carbon\Carbon;

use App\Event;
use App\EventTime;
use App\EventCategory;
use App\EventParticipant;

use App\User;
use App\FormTemplate;
use App\FormTemplateCategory;
use App\Form;
use App\FormAnswer;

class SampleEventsTableSeeder extends Seeder
{
    protected $faker;
    protected $formTemplate;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('events')->delete();
        DB::table('event_times')->delete();
        DB::table('event_participants')->delete();

        $this->faker = Faker::create();
        $this->formTemplate = FormTemplate::where('form_template_category_id', FormTemplateCategory::EVENT)->get()->first();


        echo 'Created event count:' . "\n";

        for($i = 0; $i <= 20; $i++) {

            $user = User::get()->random()->id;

            $registrationDate = $this->randomWeekday(Carbon::now(), 30);
            $startDate = $this->incrementDay(Carbon::parse($registrationDate));
            $endDate = $this->incrementDay(Carbon::parse($startDate), 1);  
            $diffInDays = $startDate->diffInDaysFiltered(function(Carbon $date) {
                return $date->isWeekday();
            }, $endDate);

            // echo $startDate . "\n";
            // echo $endDate . "\n";
            // dd($diffInDays);            

            $isSameTime = $this->faker->boolean;

        	$event = Event::create([
                'id' => $i + 1,

        		'form_template_id' => $this->formTemplate->id,
                'event_category_id' => EventCategory::get()->random()->id,

                'title' => ucfirst($this->faker->word),
                'description' => $this->faker->paragraph,
                'facilitator' => $this->faker->name,
                'color' => str_replace('#', '', $this->faker->hexcolor),
                'venue' => $this->faker->city,

                'hours' => $this->faker->numberBetween(8, 16),
                'limit' => $this->faker->boolean ? $this->faker->numberBetween(3, 5) : 0,
                'attending' => 0,

                'registration_date' => $registrationDate,
                'start_date' => $startDate,
                'end_date' => $endDate,

                'start_time' => $isSameTime ? Carbon::now()->setTime(8, 0, 0)->format('Y-m-d H:i:s') : null,
                'end_time' => $isSameTime ? Carbon::now()->setTime(17, 0, 0)->format('Y-m-d H:i:s') : null,

                'isSameTime' => $isSameTime,

                'creator_id' => $user,
                'updater_id' => $user,
        	]);
            

            if(!$isSameTime) {

                for($o = 0; $o <= $diffInDays; $o++) {

                    $time = EventTime::create([
                        'event_id' => $event->id,

                        'start_time' => Carbon::now()->setTime(8 + $o, 0, 0)->format('Y-m-d H:i:s'),
                        'end_time' => Carbon::now()->setTime(17 + $o, 0, 0)->format('Y-m-d H:i:s'),
                    ]);
                }
            }

            $participants = $this->faker->numberBetween(5, 7);
            $approversCount = $this->faker->numberBetween(1, $participants + 1);
            $approvers = 0;
            for($p = 0; $p <= $participants; $p++) {

                $user = $p < 2 ? User::find($p + 1) : null;

                $form = $this->getForm($event, $user);
                $form->setAsDraft();

                $eventParticipant = EventParticipant::create([
                    'event_id' => $event->id,
                    'participant_id' => $form->employee_id,
                    'form_id' => $form->employee_id,
                    'approver_id' => $form->employee->supervisor_id,
                    
                    'created_at' => Carbon::parse($startDate),
                    'updated_at' => Carbon::parse($startDate)
                ]);


                $approvalAll = $this->faker->boolean;
                if($approvers < ($approvalAll ? $approversCount : $approversCount - ($approversCount / 2))) {
                    
                    if(get_class($eventParticipant) == 'App\EventParticipant') {
                        $eventParticipant->setAsApproved(false);
                        $eventParticipant->approved_at = Carbon::now()->addMinutes($this->faker->numberBetween(10, 30))->format('Y-m-d H:i:s');
                        $eventParticipant->save();

                        $event->addAttending();
                    }

                    $approvers++;
                }
            }  

            echo ($i + 1) . '|';
        }      

        echo "\n";


        /* Add to scout index */
        Event::get()->searchable();        
    }

    public function getForm($event, $user = null) {
        $participant = 1;

        while(!$user && $participant) {

            $user = User::where('id', '>', 3)->get()->random();

            $participant = EventParticipant::where([
                'event_id' => $event->id,
                'participant_id' => $user->id,
            ])->exists();
        }

        $form = Form::create([
            'form_template_id' => $this->formTemplate->id,
            'employee_id' => $user->id,
            'purpose' => ucfirst($this->faker->text),
            'creator_id' => $user->id,
        ]);

        $field = FormAnswer::create([
            'form_id' => $form->id,
            'form_template_field_id' => $this->formTemplate->fields->first()->id,
            'value' => $this->faker->word,
        ]);

        return $form;
    }

    public function randomWeekday($startDate, $limit) {
        $randDate = null;

        while(!$randDate || $randDate->isWeekend()) {
            $randDate = Carbon::parse($startDate)->addDays($this->faker->numberBetween(1, $limit));
        }

        return $randDate;
    }

    public function incrementDay($date, $endDate = false) {
        $newDate = $date;

        if($endDate)
            $newDate = $newDate->addDays($this->faker->numberBetween(1, 2));

        for($i = 1; $newDate->isWeekend(); $i++) {
            $newDate = $newDate->addDays($i);
        }

        return $newDate;
    }
}
