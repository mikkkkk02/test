<?php

namespace App\Notifications\Requests;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

use App\Custom\RequestMailMessage;

use App\FormTemplateField;
use App\FormTemplateOption;

class RequestHasUpdate extends Notification implements ShouldQueue
{
    use Queueable;

    protected $tempForm;
    protected $form;
    protected $formApprover;

    protected $owner;
    protected $approver;


    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($formApprover)
    {
        $this->tempForm = $tempForm;
        $this->form = $tempForm->form;
        $this->formApprover = $formApprover;

        $this->owner = $tempForm->employee;
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
        $message = 'The details of request #' . $this->form->id . ' of ' . $this->owner->renderFullname() . ' was updated and needs your approval';

        return (new MailMessage)
                    ->subject($message)
                    ->line('')
                    ->line('Request details has been updated:')
                    ->line('Assigned to ' . $this->approver->renderFullname())
                    ->line('')
                    ->line('For more details you may also view the request by clicking the button below.')
                    ->action('View', route('temprequest.show', $this->tempForm->id));
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
            'text' => 'The request for ' . $this->form->template->name . ' of ' . $this->owner->renderFullname() . ' was updated and needs your approval!',
            'link' => route('temprequest.show', $this->form->id),
            'linkLabel' => 'View Request Update',
            'date' => date('c'),
        ];
    }
}
