<?php

namespace App\Notifications\User;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class UserWasAssigned extends Notification implements ShouldQueue
{
    use Queueable;

    protected $assigner;
    protected $assignee;


    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($assigner, $assignee)
    {
        $this->assigner = $assigner;
        $this->assignee = $assignee;
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
                    ->subject('CSG e-Workflow: An employee has set you as his/her assigner')
                    ->line('')                    
                    ->line("Hi " . $this->assignee->first_name . ",")
                    ->line('')
                    ->line($this->assigner->renderFullname() . ' has set you as one of his/her assigner');
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
            'classes' => 'fa-user bg-green',
            'text' => $this->assigner->renderFullname() . ' has set you as one of his/her assigner',
            'body' => '',
            'link' => null,
            'linkLabel' => null,
            'date' => date('c'),
        ];
    }
}
