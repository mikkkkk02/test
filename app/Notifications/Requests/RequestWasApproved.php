<?php

namespace App\Notifications\Requests;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class RequestWasApproved extends Notification implements ShouldQueue
{
    use Queueable;

    protected $form;
    protected $isUser;

    protected $owner;


    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($form, $isUser)
    {
        $this->form = $form;
        $this->isUser = $isUser;

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
        switch($this->isUser) {
            case 1:

                return (new MailMessage)
                            ->subject('CSG e-Workflow: Request update')
                            ->line('')                    
                            ->line("Hi " . $this->owner->first_name . ",")
                            ->line('')
                            ->line('Your form request #' . $this->form->id . ' have been approved!')
                            ->line('Click the button below to redirect you to the request.')
                            ->action('View', route('request.show', $this->form->id));

            break;
            case 0:

                return (new MailMessage)
                            ->subject('CSG e-Workflow: Request update')
                            ->line('')
                            ->line('The form request #' . $this->form->id . ' of ' . $this->owner->renderFullname() . ' has now been approved!')
                            ->line('Click the button below to redirect you to the request.')
                            ->action('View', route('request.show', $this->form->id));

            break;
        }
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
            'classes' => 'fa-files-o bg-green',
            'text' => $this->isUser ? 
                        'Your form request #' . $this->form->id . ' have been approved!' :
                        'The form request #' . $this->form->id . ' of ' . $this->owner->renderFullname() . ' has now been approved!',
            'body' => $this->isUser ? 
                        '' :
                        '',            
            'link' => route('ticket.show', $this->form->ticket->id),
            'linkLabel' => 'View Request',
            'date' => date('c'),
        ];
    }
}
