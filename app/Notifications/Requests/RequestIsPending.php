<?php

namespace App\Notifications\Requests;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class RequestIsPending extends Notification implements ShouldQueue
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
    public function __construct($reqform, $formApprover)
    {
        $this->form = $reqform;
        $this->formApprover = $formApprover;
        $this->owner = $reqform->employee;
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
        $message = 'The details of request #' . $this->form->id . ' of ' . $this->owner->renderFullname() . ' is still Pending and needs your approval';
        return (new MailMessage)
                    ->subject($message)
                    ->line('')
                    ->line('Request details is still pending:')
                    ->line('Assigned to ' . $this->approver->renderFullname())
                    ->line('')
                    ->line('For more details you may also view the request by clicking the button below.')
                    ->action('View', route('request.show', $this->form->id));
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
            'text' => 'The request for ' . $this->form->template->name . ' of ' . $this->owner->renderFullname() . ' still needs your approval!',
            'link' => route('request.show', $this->form->id),
            'linkLabel' => 'View Request Update',
            'date' => date('c'),        ];
    }
}
