<?php

namespace App\Notifications\Requests;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

use App\Custom\RequestMailMessage;

use App\FormTemplateField;
use App\FormTemplateOption;

class RequestHasApprover extends Notification implements ShouldQueue
{
    use Queueable;

    protected $form;
    protected $formApprover;

    protected $owner;
    protected $approver;


    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($form, $formApprover)
    {
        $this->form = $form;
        $this->formApprover = $formApprover;

        $this->owner = $form->employee;
        $this->approver = $formApprover->approver;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $message = 'New Request for ' . $this->form->template->name . ' from ' . $this->owner->renderFullname();

        /* Change message if request is being resubmitted */
        if($this->form->isResubmitting)
            $message = 'The request #' . $this->form->id . ' from ' . $this->owner->renderFullname() . ' have been withdrawn and now being resubmitted';

        /* Create form details */
        $formDetails = $this->form->renderAnswers(true);

        /* Fetch approval/disapproval URL */
        $approval = route('request.emailapprove', $this->form);
        $disapproval = route('request.emaildisapprove', $this->form);


        return (new RequestMailMessage)
                    ->subject($message)
                    ->approve($approval)
                    ->disapprove($disapproval)
                    ->line('')
                    ->line('Request has been updated:')
                    ->line('Assigned to ' . $this->approver->renderFullname())
                    ->line('')
                    ->line('For more details you may also view the request by clicking the button below.')
                    ->action('View', route('request.show', $this->form->id))
                    ->line('Form Details:')
                    ->salutation($formDetails);
    }


    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'classes' => 'fa-files-o bg-blue',
            'text' => 'The request for ' . $this->form->template->name . ' of ' . $this->owner->renderFullname() . ' needs your approval!',
            'body' => $this->form->purpose,
            'link' => route('request.show', $this->form->id),
            'linkLabel' => 'View Request',
            'date' => date('c'),
        ];
    }
}
