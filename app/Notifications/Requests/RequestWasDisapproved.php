<?php

namespace App\Notifications\Requests;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class RequestWasDisapproved extends Notification implements ShouldQueue
{
    use Queueable;

    protected $form;
    protected $disapprover;
    protected $reason;

    protected $owner;


    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($form, $disapprover, $reason)
    {
        $this->form = $form;
        $this->disapprover = $disapprover;
        $this->reason = $reason;

        $this->owner = $form->employee;
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
        return (new MailMessage)
                    ->subject('Your request for ' . $this->form->template->name . ' has been updated!')
                    ->line('')                    
                    ->line('Your request has been updated:')
                    ->line('Disapproved by ' . $this->disapprover->renderFullname())
                    ->line('Reason: "' . $this->reason . '"')
                    ->line('')
                    ->line('For more details you may also view the updates by clicking the button below.')
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
            'classes' => 'fa-files-o bg-red',
            'text' => 'Your form request has been disapproved by ' . $this->disapprover->renderFullname(),
            'body' => '',            
            'link' => route('request.show', $this->form->id),
            'linkLabel' => 'View Request',            
            'date' => date('c'),
        ];
    }
}
