<?php

use Illuminate\Database\Seeder;

use Carbon\Carbon;

use App\User;
use App\FormTemplate;
use App\FormTemplateCategory;
use App\FormTemplateField;
use App\FormTemplateOption;
use App\FormTemplateApprover;

class FormsSeeder extends Seeder
{
	protected $template;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();


        /* HR */
        // $this->createEmergencyLoanForm();
        $this->createEmployeeClearanceForm();
        // $this->createManpowerRequisitionForm();
        // $this->createOpticalClaimForm('MORE', 105211);
        // $this->createOpticalClaimForm('SNAPM', 105242);
        // $this->createOpticalClaimForm('SNAPB', 105247);
        // $this->createRequestMedicalReimbursementForm();
        // $this->createWellnessSubsidyForm('MORE', 105211);
        // $this->createWellnessSubsidyForm('SNAPM', 105242);
        // $this->createWellnessSubsidyForm('SNAPB', 105253);        


        /* OD */
        // $this->createTrainingEnrollmentForm();
        // $this->createExternalTrainingEvaluationForm();
        // $this->createLearningSessionEvaluationForm();
        // $this->createLDForm(); // A.K.A IDP Form


        /* Admin */
        // $this->createCourierForm();
        // $this->createGatePassForm();
        // $this->createMeetingReservationRoomForm();
        // $this->createOfficeEquipmentRequestForm();
        // $this->createRequestUtilityPersonnelForm();
        // $this->createTechnicalMaintenanceForm();
        // $this->createTravelOrderForm();
        // $this->createVisitorRegistrationForm(); 


        /*
        | @End Transaction
        |---------------------------------------------*/
        \DB::commit();


        /* Add to scout index */
        FormTemplate::get()->searchable();        
    }


    /*
    |-----------------------------------------------
    | @HR
    |----------------------------------------------*/

    /**
     * Create Emergency Loan default form.
     *
     * @return void
     */
    public function createEmergencyLoanForm()
    {
        $template = $this->createTemplate(
                FormTemplateCategory::FORM,
                'Emergency Loan',
                'This is the Emergency Loan Form request',
                1,
                1
            );


        /* Create fields */
        $this->createField($template, 0, 'Amount Requested', FormTemplateField::TEXTFIELD, 1);
        $this->createField($template, 1, 'Purpose', FormTemplateField::RADIOBOX, 1, [
                'Educational Loan', 'Medical Loan', 'Bereavement Loan',
            ]
        );
        $this->createField($template, 2, 'Loan Type', FormTemplateField::RADIOBOX, 1, [
                'First Time', 'Renewal',
            ]
        );
        $this->createField($template, 3, 'Previous Loan Amount', FormTemplateField::TEXTFIELD, 0);
        $this->createField($template, 4, 'Total Amount Paid', FormTemplateField::TEXTFIELD, 0);
        $this->createField($template, 5, 'Balance on Previous Loan', FormTemplateField::TEXTFIELD, 0);
        $this->createField($template, 6, 'Amount to be credited to account', FormTemplateField::TEXTFIELD, 0);
        $this->createField($template, 7, 'Payment for Tuition and Miscellaneous Fees of', FormTemplateField::TEXTFIELD, 0);
        $this->createField($template, 8, 'Please see attached Certification of Fees from', FormTemplateField::TEXTFIELD, 0);
        $this->createField($template, 9, 'Will renew loan on next semester scheduled on', FormTemplateField::TEXTFIELD, 0);


        /* Create approvers */
        FormTemplateApprover::create(['form_template_id' => $template->id, 'type' => 2, 'employee_id' => $this->getEmployeeID('roseanne.rington@snaboitiz.com') ]);
        FormTemplateApprover::create(['form_template_id' => $template->id, 'type' => 2, 'employee_id' => $this->getEmployeeID('landoy.zamora@snaboitiz.com') ]);
        FormTemplateApprover::create(['form_template_id' => $template->id, 'type' => 2, 'employee_id' => $this->getEmployeeID('mike.hosillos@snaboitiz.com') ]);


        echo "Created Emergency Loan default form \n";        
    }

    /**
     * Create Employee Clearance default form.
     *
     * @return void
     */
    public function createEmployeeClearanceForm()    
    {
        $template = $this->createTemplate(
                FormTemplateCategory::FORM,
                'Employee Clearance',
                'This is the Employee Clearance Form request',
                1,
                1
            );    


        $this->createField($template, 0, 'Type', FormTemplateField::RADIOBOX, 1, [
                'Resigned', 
                'Laid Off',
                'Terminated',
                'Returned',
                'Contract/Project Terminated',
                'Transferred',
                'Others',
            ], 1
        );

        $this->createField($template, 1, 'Last day of employment', FormTemplateField::DATEFIELD, 1);

        $this->createField($template, 2, 'Accounts Payable / Accounts Receivable', FormTemplateField::HEADER, 0);
        $this->createField($template, 3, 'Advances Operational', FormTemplateField::TEXTFIELD, 0);
        $this->createField($template, 4, 'Advances Personal', FormTemplateField::TEXTFIELD, 0);
        $this->createField($template, 5, 'Advances Others', FormTemplateField::TEXTFIELD, 0);
        $this->createField($template, 6, 'Remarks', FormTemplateField::RADIOBOX, 1, [
                'Cleared', 'Not Cleared',
            ]
        );

        $this->createField($template, 7, 'Treasury', FormTemplateField::HEADER, 0);
        $this->createField($template, 8, 'Special Fund (e.g: Revolving/Petty Cash Fund)', FormTemplateField::TEXTFIELD, 0);
        $this->createField($template, 9, 'Corporate Card', FormTemplateField::TEXTFIELD, 0);
        $this->createField($template, 10, 'Remarks', FormTemplateField::RADIOBOX, 1, [
                'Cleared', 'Not Cleared',
            ]
        );  

        $this->createField($template, 11, 'Supply Management', FormTemplateField::HEADER, 0);
        $this->createField($template, 12, 'Remarks', FormTemplateField::RADIOBOX, 1, [
                'Cleared', 'Not Cleared',
            ]
        ); 

        $this->createField($template, 13, 'SHESQ', FormTemplateField::HEADER, 0);
        $this->createField($template, 14, 'Remarks', FormTemplateField::RADIOBOX, 1, [
                'Cleared', 'Not Cleared',
            ]
        ); 

        $this->createField($template, 15, 'Administration', FormTemplateField::HEADER, 0);
        $this->createField($template, 16, '', FormTemplateField::CHECKBOX, 1, [
                'Smart Billing', 
                'Globe Tattoo', 
                'Returned Mobile Phone/Unit', 
                'Vehicle Keys', 
                'Other Items (FATA)',
            ]
        );         
        $this->createField($template, 17, 'Remarks', FormTemplateField::RADIOBOX, 1, [
                'Cleared', 'Not Cleared',
            ]
        );

        $this->createField($template, 18, 'Information Technology', FormTemplateField::HEADER, 0);
        $this->createField($template, 19, 'Checklist', FormTemplateField::CHECKBOX, 1, [
                'Laptop and Other Accessories', 
                'Deactivate Email Address', 
                'Local Lines',
            ]
        );         
        $this->createField($template, 20, 'Remarks', FormTemplateField::RADIOBOX, 1, [
                'Cleared', 'Not Cleared',
            ]
        );

        $this->createField($template, 21, 'Immediate Leader', FormTemplateField::HEADER, 0);
        $this->createField($template, 22, 'Checklist', FormTemplateField::CHECKBOX, 1, [
                'All materials and documents, soft and hard copy relative to the current function and position have been surrendered and accounted for.',
            ]
        );         
        $this->createField($template, 23, 'Remarks', FormTemplateField::RADIOBOX, 1, [
                'Cleared', 'Not Cleared',
            ]
        );

        $this->createField($template, 24, 'Organization Development', FormTemplateField::HEADER, 0);
        $this->createField($template, 25, 'Checklist', FormTemplateField::CHECKBOX, 1, [
                'Undergone Exit Interview',
                'Cleared from Training Agreement',
                'Submitted photocopy of Certificate of Trainings attended.',
                'Returned Training Materials, books and other paraphernalia.',
            ]
        );         
        $this->createField($template, 26, 'Remarks', FormTemplateField::RADIOBOX, 1, [
                'Cleared', 'Not Cleared',
            ]
        );

        $this->createField($template, 27, 'HR Services', FormTemplateField::HEADER, 0);
        $this->createField($template, 28, 'Checklist', FormTemplateField::CHECKBOX, 1, [
                'Resignation Letter signed by immediate leader.',
                'Completed Employee Clearance Form',
                'Cancellation of Biometric Access and Oracle',
                'Company ID, Medical and Dental Cards',
                'Cancellation of Medical/Dental Coverage',
                'Employee Handbook',
                'Release of Last Pay, Quitclaim and Certificate of Employment',
            ]
        );         
        $this->createField($template, 29, 'Remarks', FormTemplateField::RADIOBOX, 1, [
                'Cleared', 'Not Cleared',
            ]
        );

        $this->createField($template, 30, 'Employee to return on for the notarization of Quitclaim and release of Final Pay', FormTemplateField::DATEFIELD, 1);


        echo "Created Emergency Clearance default form \n";           
    }

    /**
     * Create Manpower Requisition default form.
     *
     * @return void
     */
    public function createManpowerRequisitionForm()
    {
        $template = $this->createTemplate(
                FormTemplateCategory::FORM,
                'Manpower Requisition',
                'This is the Manpower Requisition Form request',
                1
            );


        $this->createField($template, 0, 'Position Title', FormTemplateField::TEXTFIELD, 1);
        $this->createField($template, 1, 'Number Needed', FormTemplateField::TEXTFIELD, 1);
        $this->createField($template, 2, 'Manpower Requirement', FormTemplateField::TABLE, 0, [
                [ 'value' => 'Type of Employment', 'type' => FormTemplateOption::DROPDOWN, 'type_value' => 
                    'Probationary/Regular, Contractual, Project, Consultant, Others'
                ],
                [ 'value' => 'Hiring Company', 'type' => FormTemplateOption::DROPDOWN, 'type_value' => 
                    'MORE, SNAPBI, SNAPMI, SNAPGEN'
                ],
                [ 'value' => 'Location', 'type' => FormTemplateOption::DROPDOWN, 'type_value' => 
                    'Taguig, Binga, Ambuklao, Magat, Others'
                ],
                [ 'value' => 'Cost Center', 'type' => FormTemplateOption::TEXTFIELD, 'type_value' => null ],                
            ]
        );
        $this->createField($template, 3, 'Section/Department', FormTemplateField::TEXTFIELD, 1);
        $this->createField($template, 4, 'Date Needed', FormTemplateField::TEXTFIELD, 1);
        $this->createField($template, 5, 'Justification/Reason for Requisition', FormTemplateField::RADIOBOX, 1, [ 
                'Plantilla completion/vacancy', 'Replacement for', 'New Position because of', 'Temporary replacement due to'
            ], true
        );
        $this->createField($template, 6, 'Please specify:', FormTemplateField::TEXTFIELD, 1);
        $this->createField($template, 7, 'Brief Job Description', FormTemplateField::TEXTAREA, 1);
        $this->createField($template, 8, 'Manpower Specification:', FormTemplateField::TABLE, 0, [
                [ 'value' => 'Category', 'type' => FormTemplateOption::TEXTFIELD, 'type_value' => null ],
                [ 'value' => 'Must/s', 'type' => FormTemplateOption::TEXTFIELD, 'type_value' => null ],
                [ 'value' => 'Preferred', 'type' => FormTemplateOption::TEXTFIELD, 'type_value' => null ],
            ]
        );
        $this->createField($template, 9, 'Requisitioning Leader', FormTemplateField::TEXTFIELD, 0);


        /* Create approvers */
        FormTemplateApprover::create(['form_template_id' => $template->id, 'type' => 0, 'type_value' => 0, 'employee_id' => null ]);
        FormTemplateApprover::create(['form_template_id' => $template->id, 'type' => 2, 'employee_id' => $this->getEmployeeID('landoy.zamora@snaboitiz.com') ]);
        FormTemplateApprover::create(['form_template_id' => $template->id, 'type' => 2, 'employee_id' => $this->getEmployeeID(105134) ]);
        FormTemplateApprover::create(['form_template_id' => $template->id, 'type' => 2, 'employee_id' => $this->getEmployeeID(106149) ]);


        echo "Created Manpower Requisition default form \n";
    }

    /**
     * Create Optical Assistance Reimbursement Claim default form.
     *
     * @return void
     */
    public function createOpticalClaimForm($company, $approver)
    {
        $template = $this->createTemplate(
                FormTemplateCategory::FORM,
                $company . ' Optical Assistance Reimbursement Claim',
                'This is the ' . $company . ' Optical Assistance Reimbursement Claim Form request',
                1,
                1
            );


        /* Create fields */
        $this->createField($template, 1, 'Amount Being Claimed', FormTemplateField::TEXTFIELD, 1);
        $this->createField($template, 2, 'Eyewear Provider', FormTemplateField::TEXTFIELD, 1);
        $this->createField($template, 3, 'Address of Provider', FormTemplateField::TEXTFIELD, 1);
        $this->createField($template, 4, 'Contact No. of Provider', FormTemplateField::TEXTFIELD, 1);
        $this->createField($template, 5, 'Please scan and attach the following documents and submit the originals to HR', FormTemplateField::CHECKBOX, 1, 
            [ 
                'Medical Certificate from a certified Optometrist/Opthalmologist',
                'Medical Record indicating eye readings both manual diagnosis results and computerized results with recommendations',
                'Recent prescription in the name of the employee signed by the Optometrist/Opthalmologist for corrective eyeglasses',
                'Receipts',
            ]
        );      


        /* Create approvers */
        FormTemplateApprover::create([ 'form_template_id' => $template->id, 'type' => 2, 'employee_id' => $this->getEmployeeID($approver) ]);
        FormTemplateApprover::create([ 'form_template_id' => $template->id, 'type' => 2, 'employee_id' => $this->getEmployeeID('landoy.zamora@snaboitiz.com') ]);


        echo "Created " . $company . " Optical Assistance Reimbursement Claim default form \n";
    }

    /**
     * Create Request Medical Reimbursement default form.
     *
     * @return void
     */
    public function createRequestMedicalReimbursementForm()    
    {
        $template = $this->createTemplate(
            FormTemplateCategory::FORM,
            'Medical Reimbursement',
            'This is the Request Medical Reimbursement Form request',
            1,
            1
        );


        /* Create fields */
        $this->createField($template, 0, 'Summary', FormTemplateField::TABLE, 0, [
                [ 'value' => 'Consulation Date', 'type' => FormTemplateOption::DATEFIELD, 'type_value' => null ],
                [ 'value' => 'Receipt Date', 'type' => FormTemplateOption::DATEFIELD, 'type_value' => null ],
                [ 'value' => 'Official Receipt No.', 'type' => FormTemplateOption::TEXTFIELD, 'type_value' => null ],
                [ 'value' => 'Category', 'type' => FormTemplateOption::DROPDOWN, 'type_value' => 'Medicines, Consultation, In-Patient' ],
                [ 'value' => 'Amount of Claim/s', 'type' => FormTemplateOption::TEXTFIELD, 'type_value' => null ],                
            ]
        );


        echo "Created Request Medical Reimbursement form \n";
    }

    /**
     * Create Wellness Sudsidy default form.
     *
     * @return void
     */
    public function createWellnessSubsidyForm($company, $approver)
    {
        $template = $this->createTemplate(
                FormTemplateCategory::FORM,
                $company . ' Wellness Subsidy',
                'This is the ' . $company . ' Wellness Subsidy Form request',
                1
            );


        /* Create fields */
        $this->createField($template, 0, 'Purpose', FormTemplateField::RADIOBOX, 1, [
                'Fitness Center Enrollment', 'Fitness Event Registration', 'Dental Services', 'Vaccination', 'Vitamins'
            ], true
        );
        $this->createField($template, 2, 'Name of Doctor/Professional Trainer/Instructor', FormTemplateField::TEXTFIELD, 1);
        $this->createField($template, 3, 'Name of Hospital/Clinic/Fitness Center/Event Organizer', FormTemplateField::TEXTFIELD, 1);


        /* Create approvers */
        FormTemplateApprover::create([ 'form_template_id' => $template->id, 'type' => 2, 'employee_id' => $this->getEmployeeID($approver) ]);
        FormTemplateApprover::create([ 'form_template_id' => $template->id, 'type' => 2, 'employee_id' => $this->getEmployeeID('landoy.zamora@snaboitiz.com') ]);


        echo "Created " . $company . " Wellness Sudsidy default form \n";
    } 


    /*
    |-----------------------------------------------
    | @OD
    |----------------------------------------------*/ 

    /**
     * Create Training Enrollment default form.
     *
     * @return void
     */
    public function createTrainingEnrollmentForm()    
    {
        //
    }

    /**
     * Create External Training Evaluation default form.
     *
     * @return void
     */
    public function createExternalTrainingEvaluationForm()    
    {
        $template = $this->createTemplate(
                FormTemplateCategory::FORM,
                'External Training Evaluation',
                'This is the External Training Evaluation Form request',
                2
            );


        /* Create fields */
        $this->createField($template, 0, 'Training Attended', FormTemplateField::TEXTFIELD, 1);
        $this->createField($template, 1, 'Venue', FormTemplateField::TEXTFIELD, 1);
        $this->createField($template, 2, 'Date of Attendance', FormTemplateField::DATEFIELD, 1);
        $this->createField($template, 3, 'Training Provider', FormTemplateField::TEXTFIELD, 1);
        $this->createField($template, 4, 'Objectives are clear', FormTemplateField::RADIOBOX, 1, [ '1', '2', '3', '4', '5' ]);
        $this->createField($template, 5, 'Content is relevant', FormTemplateField::RADIOBOX, 1, [ '1', '2', '3', '4', '5' ]);
        $this->createField($template, 6, 'Sequence of topics is appropriate', FormTemplateField::RADIOBOX, 1, [ '1', '2', '3', '4', '5' ]);
        $this->createField($template, 7, 'Exercises have been helpful for learning', FormTemplateField::RADIOBOX, 1, [ '1', '2', '3', '4', '5' ]);
        $this->createField($template, 8, 'Course length is adequate and pace easy to follow', FormTemplateField::RADIOBOX, 1, [ '1', '2', '3', '4', '5' ]);
        $this->createField($template, 9, 'Knowledgeable on the subject', FormTemplateField::RADIOBOX, 1, [ '1', '2', '3', '4', '5' ]);
        $this->createField($template, 10, 'Delivered the course clearly', FormTemplateField::RADIOBOX, 1, [ '1', '2', '3', '4', '5' ]);
        $this->createField($template, 11, 'Encouraged participation', FormTemplateField::RADIOBOX, 1, [ '1', '2', '3', '4', '5' ]);
        $this->createField($template, 12, 'Facility is conductive to learning', FormTemplateField::RADIOBOX, 1, [ '1', '2', '3', '4', '5' ]);
        $this->createField($template, 13, 'Training material is complete', FormTemplateField::RADIOBOX, 1, [ '1', '2', '3', '4', '5' ]);
        $this->createField($template, 14, 'Overall Rating of the course', FormTemplateField::RADIOBOX, 1, [ '1', '2', '3', '4', '5' ]);
        $this->createField($template, 15, 'My Expectations are met', FormTemplateField::RADIOBOX, 1, [ '1', '2', '3', '4', '5' ]);
        $this->createField($template, 16, 'What activities or resources did you find most valuable in the training?', FormTemplateField::TEXTAREA, 1);
        $this->createField($template, 17, 'Would you recommend this training to others? Why or why not?', FormTemplateField::TEXTAREA, 1);


        echo "Created External Training Evaluation default form \n";
    }

    /**
     * Create Learning Session Evaluation default form.
     *
     * @return void
     */
    public function createLearningSessionEvaluationForm()    
    {
        $template = $this->createTemplate(
                FormTemplateCategory::FORM,
                'Learning Session Evaluation',
                'This is the Learning Session Evaluation Form request',
                2
            );


        /* Create fields */
        $this->createField($template, 0, 'Training Attended', FormTemplateField::TEXTFIELD, 1);
        $this->createField($template, 1, 'Venue', FormTemplateField::TEXTFIELD, 1);
        $this->createField($template, 2, 'Date of Attendance', FormTemplateField::DATEFIELD, 1);
        $this->createField($template, 3, 'Training Provider', FormTemplateField::TEXTFIELD, 1);
        $this->createField($template, 4, 'Objectives are clear', FormTemplateField::RADIOBOX, 1, [ '1', '2', '3', '4', '5' ]);
        $this->createField($template, 5, 'Content is relevant', FormTemplateField::RADIOBOX, 1, [ '1', '2', '3', '4', '5' ]);
        $this->createField($template, 6, 'Exercises have been helpful for learning', FormTemplateField::RADIOBOX, 1, [ '1', '2', '3', '4', '5' ]);
        $this->createField($template, 7, 'Knowledgeable on the subject', FormTemplateField::RADIOBOX, 1, [ '1', '2', '3', '4', '5' ]);
        $this->createField($template, 8, 'Delivered the course clearly', FormTemplateField::RADIOBOX, 1, [ '1', '2', '3', '4', '5' ]);
        $this->createField($template, 9, 'Encouraged participation', FormTemplateField::RADIOBOX, 1, [ '1', '2', '3', '4', '5' ]);
        $this->createField($template, 10, 'Overall Rating of the course', FormTemplateField::RADIOBOX, 1, [ '1', '2', '3', '4', '5' ]);
        $this->createField($template, 11, 'My Expectations are met', FormTemplateField::RADIOBOX, 1, [ '1', '2', '3', '4', '5' ]);
        $this->createField($template, 12, 'What activities or resources did you find most valuable in the training??', FormTemplateField::TEXTAREA, 1);
        $this->createField($template, 13, "What are the facilitator's strengths? Areas for improvement?", FormTemplateField::TEXTAREA, 1);
        $this->createField($template, 14, 'How else can we improve our training programs?', FormTemplateField::TEXTAREA, 1);    
        $this->createField($template, 15, 'Would you recommend this training to others? Why or Why not?', FormTemplateField::TEXTAREA, 1);    


        echo "Created Learning Session Evaluation default form \n";
    }

    /**
     * Create IDP default form.
     *
     * @return void
     */
    public function createIDPForm()    
    {
        //
    }    

    /**
     * Create L&D default form.
     *
     * @return void
     */
    public function createLDForm()
    {
        $template = $this->createTemplate(
                FormTemplateCategory::LD,
                'Learning & Development Enrollment',
                'This is the Learning & Development Form request',
                2
            );


        /* Create fields */
        $this->createField($template, 0, 'Training Title', FormTemplateField::TEXTFIELD, 1);
        $this->createField($template, 1, 'Training Venue', FormTemplateField::TEXTFIELD, 1);
        $this->createField($template, 2, 'Training Date', FormTemplateField::DATEFIELD, 1);
        $this->createField($template, 3, 'Training Provider', FormTemplateField::TEXTFIELD, 1);
        $this->createField($template, 4, 'Objective of attending the program', FormTemplateField::TEXTAREA, 1);
        $this->createField($template, 5, 'Program/Course is part of your', FormTemplateField::RADIOBOX, 1, ['Individual Development Plan', 'Mandatory Programs/Updates etc.'], true);
        $this->createField($template, 6, 'Application to work', FormTemplateField::TEXTAREA, 1);
        $this->createField($template, 6, 'Competency Addressed', FormTemplateField::TEXTFIELD, 1);
        // $this->createField($template, 7, 'Training Investments', FormTemplateField::HEADER, 0);
        // $this->createField($template, 8, 'Course Fee', FormTemplateField::TEXTFIELD, 1);
        // $this->createField($template, 9, 'Accomodation', FormTemplateField::TEXTFIELD, 0);
        // $this->createField($template, 10, 'Meals', FormTemplateField::TEXTFIELD, 0);
        // $this->createField($template, 11, 'Transport', FormTemplateField::TEXTFIELD, 0);
        // $this->createField($template, 12, 'Others', FormTemplateField::TEXTFIELD, 0);
        $this->createField($template, 14, 'Charge to', FormTemplateField::RADIOBOX, 1, ['MORE', 'SNAP - Magat', 'SNAP - Benguet', 'SNAP - Gen']);


        /* Create approvers */
        for($p = 0; $p < 2; $p++) {
            $approver = FormTemplateApprover::create([
                'form_template_id' => $template->id,
                'type' => $p,
            ]);
        }    


        echo "Created L&D default form \n";
    }


    /*
    |-----------------------------------------------
    | @Admin
    |----------------------------------------------*/

    /**
     * Create Courier default form.
     *
     * @return void
     */
    public function createCourierForm()    
    {
        //
    }

    /**
     * Create Gate Pass default form.
     *
     * @return void
     */
    public function createGatePassForm()
    {
        $template = $this->createTemplate(
                FormTemplateCategory::FORM,
                'Gate Pass',
                'This is the Gate Pass Form request',
                0
            );


        /* Create fields */
        $this->createField($template, 0, 'Floor Unit', FormTemplateField::TEXTFIELD, 1);
        $this->createField($template, 1, 'Authorized Representative', FormTemplateField::TEXTFIELD, 1);
        $this->createField($template, 2, 'Please allow Mr/Ms', FormTemplateField::TEXTFIELD, 1);
        $this->createField($template, 3, 'To take out or take in from our premises the following items', FormTemplateField::TEXTAREA, 1);


        echo "Created Gate Pass default form \n";    
    }   

    /**
     * Create Meeting Reservation default form.
     *
     * @return void
     */
    public function createMeetingReservationRoomForm()    
    {
        $template = $this->createTemplate(
                FormTemplateCategory::FORM,
                'Meeting Room Reservation',
                'This is the Meeting Room Reservation Form request',
                0
            );


        /* Create fields */
        $this->createField($template, 0, 'Title of Meeting', FormTemplateField::TEXTFIELD, 1);
        $this->createField($template, 1, 'Group/Team', FormTemplateField::TEXTFIELD, 1);
        $this->createField($template, 2, 'Date Needed', FormTemplateField::DATEFIELD, 1);
        $this->createField($template, 3, 'Start Time', FormTemplateField::TIME, 1);
        $this->createField($template, 4, 'End Time', FormTemplateField::TIME, 1);
        $this->createField($template, 5, 'No. of Attendees', FormTemplateField::TEXTFIELD, 1);
        $this->createField($template, 6, 'Request For', FormTemplateField::CHECKBOX, 1, [
                'Videocon Facilities', 'Projector', 'Coffee/Tea/Water', 'Utility Personnel Assistance'
            ], true
        );
        $this->createField($template, 7, 'Remarks', FormTemplateField::TEXTAREA, 1);


        echo "Created Meeting Reservation default form \n"; 
    }

    /**
     * Create Office Equipment Request default form.
     *
     * @return void
     */
    public function createOfficeEquipmentRequestForm()    
    {
        $template = $this->createTemplate(
                FormTemplateCategory::FORM,
                'Request Utility Personnel',
                'This is the Request Utility Personnel Form request',
                0
            );


        /* Create fields */
        $this->createField($template, 0, 'Date Needed', FormTemplateField::DATEFIELD, 1);
        $this->createField($template, 1, 'Time Needed', FormTemplateField::TEXTFIELD, 1);
        $this->createField($template, 2, 'Task', FormTemplateField::CHECKBOX, 1, [
                'Special Cleaning', 'Delivery Assistance', 'Hauling Assistance', 'Outside Errand', 'Messengerial', 'Documents Routing',
            ], true
        );
        $this->createField($template, 3, 'Remarks', FormTemplateField::TEXTAREA, 1);
        $this->createField($template, 4, 'Requested', FormTemplateField::TEXTFIELD, 1);
        $this->createField($template, 5, 'Group/Team', FormTemplateField::TEXTFIELD, 1);


        echo "Created Request Utility Personnel default form \n"; 
    }

    /**
     * Create Request Utility Personnel default form.
     *
     * @return void
     */
    public function createRequestUtilityPersonnelForm()    
    {
        $template = $this->createTemplate(
                FormTemplateCategory::FORM,
                'Office Equipment Request',
                'This is the Office Equipment Request Form request',
                0
            );


        /* Create fields */
        $this->createField($template, 0, 'Date Needed', FormTemplateField::DATEFIELD, 1);
        $this->createField($template, 1, 'Time Needed', FormTemplateField::TEXTFIELD, 1);
        $this->createField($template, 2, 'Equipment', FormTemplateField::CHECKBOX, 1, [
                'LCD Projector', 'Projector Screen', 'Sound System', 'Shredder', 'Paper Cutter', 'Binding Machine', 'Clicker', 'Voice Recorder', 'Microphone', 'Microphone Stand', 'Typewriter', 'Laminating Machine' 
            ], true
        );
        $this->createField($template, 3, 'Remarks', FormTemplateField::TEXTAREA, 1);
        $this->createField($template, 4, 'Group/Team', FormTemplateField::TEXTFIELD, 1);


        echo "Created Office Equipment Request default form \n"; 
    }

    /**
     * Create Technical Maintenance default form.
     *
     * @return void
     */
    public function createTechnicalMaintenanceForm()    
    {
        $template = $this->createTemplate(
                FormTemplateCategory::FORM,
                'Technical Maintenance',
                'This is the Technical Maintenance Form request',
                0
            );


        /* Create fields */
        $this->createField($template, 0, 'Date', FormTemplateField::DATEFIELD, 1);
        $this->createField($template, 1, 'Time', FormTemplateField::TEXTFIELD, 1);
        $this->createField($template, 2, 'Request', FormTemplateField::CHECKBOX, 1, [
                'Aircon Technician', 'Electrical Technician', 
            ], true
        );
        $this->createField($template, 3, 'Request', FormTemplateField::CHECKBOX, 1, [
                '10th Floor', '11th Floor', 
            ], true
        );
        $this->createField($template, 4, 'Location', FormTemplateField::TEXTFIELD, 1);


        echo "Created Technical Maintenance default form \n"; 
    }

    /**
     * Create Travel Order default form.
     *
     * @return void
     */
    public function createTravelOrderForm()
    {
        $template = $this->createTemplate(
                FormTemplateCategory::FORM,
                'Travel Order',
                'This is the Travel Order Form request',
                0
            );


        /* Create fields */
        $this->createField($template, 0, 'Travel Details', FormTemplateField::TABLE, 0, [
                [ 'value' => 'Date of Travel', 'type' => FormTemplateOption::DATEFIELD, 'type_value' => null ],
                [ 'value' => 'From', 'type' => FormTemplateOption::TEXTFIELD, 'type_value' => null ],
                [ 'value' => 'ETD (From)', 'type' => FormTemplateOption::DATEFIELD, 'type_value' => null ],
                [ 'value' => 'To', 'type' => FormTemplateOption::TEXTFIELD, 'type_value' => null ],
                [ 'value' => 'ETD (To)', 'type' => FormTemplateOption::DATEFIELD, 'type_value' => null ],
                [ 'value' => 'Company', 'type' => FormTemplateOption::DROPDOWN, 'type_value' => 'MORE,SNAPM,SNAPB-Amb,SNAPB-Bng,SNAPG' ],
                [ 'value' => 'Cost Center Location', 'type' => FormTemplateOption::TEXTFIELD, 'type_value' => null ],
                [ 'value' => 'Remarks', 'type' => FormTemplateOption::TEXTFIELD, 'type_value' => null ],
            ]
        );
        $this->createField($template, 1, 'Hotel/Staffhouse', FormTemplateField::RADIOBOX, 1, ['No', 'Yes']);
        $this->createField($template, 2, 'Type of Room', FormTemplateField::RADIOBOX, 0, ['Single', 'Shared']);
        $this->createField($template, 3, 'Airport Pickup', FormTemplateField::RADIOBOX, 1, ['No', 'Yes']);
        $this->createField($template, 4, 'Pickup point', FormTemplateField::TABLE, 0, [
                [ 'value' => 'Airport', 'type' => FormTemplateOption::TEXTFIELD, 'type_value' => null ],
                [ 'value' => 'Bus Station', 'type' => FormTemplateOption::TEXTFIELD, 'type_value' => null ],
            ]
        );
        $this->createField($template, 5, 'Other Requests', FormTemplateField::TEXTAREA, 0);


        /* Create approvers */
        FormTemplateApprover::create(['form_template_id' => $template->id, 'type' => 0, 'type_value' => 0, 'employee_id' => null ]);


        echo "Created Travel Order default form \n";       
    }    

    /**
     * Create Visitor Registration default form.
     *
     * @return void
     */
    public function createVisitorRegistrationForm()    
    {
        $template = $this->createTemplate(
                FormTemplateCategory::FORM,
                'Visitor Registration',
                'This is the Visitor Registration Form request',
                0
            );


        /* Create fields */
        $this->createField($template, 0, 'Guest Name', FormTemplateField::TEXTFIELD, 1);
        $this->createField($template, 1, 'Company/Organization', FormTemplateField::TEXTFIELD, 1);
        $this->createField($template, 2, 'Contact Number', FormTemplateField::TEXTFIELD, 1);
        $this->createField($template, 3, 'Person to Visit', FormTemplateField::TEXTFIELD, 1);
        $this->createField($template, 4, 'Company to Visit', FormTemplateField::CHECKBOX, 1, [
                'MORE', 'SNAPG', 
            ], true
        );
        $this->createField($template, 5, 'Floor', FormTemplateField::CHECKBOX, 1, [
                '10th Floor', '11th Floor', 
            ], true
        );
        $this->createField($template, 6, 'Date of Visit', FormTemplateField::DATEFIELD, 1);
        $this->createField($template, 7, 'Expected Time', FormTemplateField::TEXTFIELD, 1);


        echo "Created Visitor Registration default form \n"; 
    }  


    /*
    |-----------------------------------------------
    | @Methods
    |----------------------------------------------*/

    /**
     * Create form template field
     *
     * @return void
     */
    public function createField($template, $sort, $label, $type, $isRequired, $options = null, $hasOthers = false) {

		$field = FormTemplateField::create([
			'form_template_id' => $template->id,
			'sort' => $sort,
			'label' => $label,
			'type' => $type,
            'hasOthers' => $hasOthers,
            'isRequired' => $isRequired,
		]);

		if($options) {

            switch($type) {
                case FormTemplateField::RADIOBOX: case FormTemplateField::CHECKBOX: case FormTemplateField::DROPDOWN:

                    foreach ($options as $key => $option) {
                        $option = FormTemplateOption::create([
                            'form_template_field_id' => $field->id,
                            'sort' => $key,
                            'value' => $option,
                        ]);
                    }

                break;
                case FormTemplateField::TABLE:

                    foreach ($options as $key => $option) {
                        $option = FormTemplateOption::create([
                            'form_template_field_id' => $field->id,
                            'sort' => $key,
                            'value' => $option['value'],
                            'type' => $option['type'],
                            'type_value' => $option['type_value'],
                        ]);
                    }

                break;                
            }
		}
    }


    /**
     * Create form template field
     *
     * @return void
     */
    public function createTemplate($category, $name, $description, $type, $enableAttachment = false) {

        $employeeID = $this->getEmployeeID('babygrace.umali@snaboitiz.com');

        return FormTemplate::create([
            'form_template_category_id' => $category,
            'name' => $name,
            'description' => $description,

            'type' => $type,

            'sla' => 3,
            'sla_option' => 0,
            'approval_option' => 0,
            'priority' => 1,

            'enableAttachment' => $enableAttachment,
            
            'creator_id' => $employeeID,
            'updater_id' => $employeeID,

            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);        
    }

    public function getEmployeeID($string) 
    {
        return User::withTrashed()->where('id', $string)
                    ->orWhere('email', $string)
                    ->get()
                    ->first()
                    ->id;
    }
}
