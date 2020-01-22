<?php

use Illuminate\Database\Seeder;

use Faker\Factory as Faker;
use Carbon\Carbon;

use App\Requests;

use App\User;
use App\FormTemplate;
use App\FormTemplateField;
use App\FormTemplateOption;
use App\FormTemplateCategory;
use App\FormTemplateApprover;
use App\FormApprover;
use App\Form;
use App\FormAnswer;
use App\Ticket;

class SampleRequestsTableSeeder extends Seeder
{
    protected $faker;

    protected $users;
    protected $formTemplateEvent;
    protected $formTemplateLD;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('forms')->delete();
        DB::table('form_approvers')->delete();
        DB::table('tickets')->delete();


        $this->faker = Faker::create();
        $this->users = User::get();


        /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();


        echo 'Created requests count (WARNING! THIS WILL TAKE A WHILE):' . "\n";

        for($i = 1; $i <= 12; $i++) {
            foreach($this->users as $key => $user) {

                $month = Carbon::createFromDate(Carbon::now()->year, $i, 1);

                $form = $this->getForm($month, $user);
                $this->addApprovers($form);

                $this->approveForm($form);
                $this->approveTicket($user, $month, $form);
            }

            echo $month->format('M') . '|';
        } 
        
        echo "\n";  


        /*
        | @End Transaction
        |---------------------------------------------*/
        \DB::commit();


        /* Add to scout index */
        Form::get()->searchable();
        Ticket::get()->searchable();
    }

    public function approveTicket($user, $month, $form) {

        $ticket = $form->ticket;

        $technician = $user->getDepartment()->employees()->get()->random();
        $created = Carbon::createFromDate($month->year, $month->month, $this->faker->numberBetween(1, 29));

        /* Assign random technician */
        $ticket->technician()->associate($technician);
        $ticket->save();        

        /* Update status */
        $randStatus = $this->faker->randomElement(
            array(Ticket::CLOSE, Ticket::CLOSE, Ticket::CLOSE, Ticket::CLOSE, Ticket::CLOSE, Ticket::CLOSE, Ticket::CLOSE, Ticket::CLOSE,  Ticket::ONHOLD, Ticket::CANCELLED)
        );

        $ticket->status = $randStatus;

        if($randStatus == Ticket::CLOSE) {

            $randState = $this->faker->randomElement(
                array(Ticket::ONTIME, Ticket::ONTIME, Ticket::ONTIME, Ticket::ONTIME, Ticket::ONTIME, Ticket::ONTIME, Ticket::ONTIME, Ticket::DELAYED)
            );

            $ticket->state = $randState;

            switch ($randState) {
                case Ticket::ONTIME:
                
                    $createDate = Carbon::parse($created)->addDays($this->faker->numberBetween(1, $ticket->sla));

                    $ticket->date_closed = $createDate;
                    $ticket->updated_at = $createDate;

                break;
                case Ticket::DELAYED:
                
                    $createDate = Carbon::parse($created)->addDays($this->faker->numberBetween($ticket->sla, 5));

                    $ticket->date_closed = $createDate;
                    $ticket->updated_at = $createDate;                    

                break;                
            }
        }

        $ticket->start_date = Carbon::parse($created);
        $ticket->created_at = Carbon::parse($created);
        $ticket->save();        
    }

    public function approveForm($form) {
        foreach ($form->approvers as $key => $approver) {
            $approver->approve();
        }

        $form->status = Form::APPROVED;

        /* Create the ticket */
        $form->generateTicket();
        $form->save();
    }

    public function addApprovers($form) {

        /* Loop each form template approvers */
        foreach($form->template->approvers as $key => $approver) {

            $approverID = null;
            $sortID = $key;
            $vars = [];


            /* Fetch the approver */
            switch($approver->type) {
                case FormTemplateApprover::IMMEDIATE_LEADER:
                
                    $approverID = $form->employee->getImmediateLeaderID();

                break;
                case FormTemplateApprover::NEXT_LEVEL_LEADER:
                
                    $approverID = $form->employee->getNextLevelLeaderID();

                break;
                case FormTemplateApprover::EMPLOYEE:

                    $employee = $approver->employee;
                    $employeeID = null;


                    /* Check if set employee is on vacation */
                    if($employee->isOnVacation())
                        $employeeID = $employee->proxy->id;
                    
                    $approverID = $employeeID || $employee->id;

                break;
            }

            /* Check if approver exists */
            if($approverID) {

                /* Set variable */
                $vars['form_id'] = $form->id;
                $vars['form_template_approver_id'] = $approver->id;
                $vars['approver_id'] = $approverID;
                $vars['type'] = $approver->type;
                $vars['sort'] = $sortID;


                /* Create the form approver */
                $formApprover = FormApprover::create($vars);
            }
        }
    }

    public function getForm($month, $user) {

        $formTemplate = FormTemplate::where('form_template_category_id', '!=', FormTemplateCategory::EVENT)
                                    ->get()
                                    ->random();

        
        $created = Carbon::parse($month)->addDays($this->faker->numberBetween(1, 29));
        $updated = Carbon::parse($created)->addDays($this->faker->numberBetween(1, 5));

        $form = Form::create([
            'form_template_id' => $formTemplate->id,
            'employee_id' => $user->id,
            'purpose' => ucfirst($this->faker->text),

            'total_cost' => $formTemplate->category->forLearning() ? $this->faker->numberBetween(10000, 50000) : 0,

            'creator_id' => $user->id,

            'created_at' => $created,
            'updated_at' => $updated,
        ]);

		/* Loop each form template fields */
        foreach($formTemplate->fields as $field) {

            /* No need to create if the type is header & paragraph */
            if($field->type != FormTemplateField::HEADER && $field->type != FormTemplateField::PARAGRAPH) {

                $fields[$field->id] = [];

                switch($field->type) {
                	case FormTemplateField::TEXTFIELD: case FormTemplateField::TEXTAREA:

                        $fields[$field->id] = $field->type == FormTemplateField::TEXTFIELD ? $this->faker->word : $this->faker->paragraph;

                	break;
                    case FormTemplateField::DATEFIELD:
                        
                        $fields[$field->id] = Carbon::parse($created)->addDays($this->faker->numberBetween(1, 5));

                    break;
                    case FormTemplateField::RADIOBOX: case FormTemplateField::CHECKBOX:

                        $fields[$field->id][] = $field->options()->get()->random()->id;

                    break;
                    case FormTemplateField::TABLE:

                        /* Loop through the field column */
                        foreach($field->options as $option) {

                            switch($option->type) {
                                case FormTemplateOption::TEXTFIELD:

                                    $fields[$field->id][0][$option->id] = $this->faker->word;

                                break;
                                case FormTemplateOption::DATEFIELD:
                                    
                                    $fields[$field->id][0][$option->id] = Carbon::parse($created)->addDays($this->faker->numberBetween(1, 5))->format('Y-m-d');

                                break;
                                case FormTemplateOption::NUMBER:

                                    $fields[$field->id][0][$option->id] = $this->faker->numberBetween(10, 500);

                                break;
                            }
                        }                    

                    break;                                    
                }

                $form->addAnswers($fields);                
            }
        }

        return $form;
    }    
}
