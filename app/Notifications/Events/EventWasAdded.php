<?php

namespace App\Notifications\Events;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class EventWasAdded extends Notification implements ShouldQueue
{
    use Queueable;

    protected $event;
    protected $user;


    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($event, $user)
    {
        $this->event = $event;
        $this->user = $user;
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
                    ->subject('New Event ' . $this->event->title . ' has been added!')
                    ->line('')
                    ->line($this->user->renderFullname() . ' has added a new event, ' . $this->event->title)
                    ->line('')
                    ->line('For more details you may also view the request by clicking the button below.')
                    ->action('View', route('event.show', $this->event->id));
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
            'classes' => 'fa-calendar bg-green',            
            'text' => 'New Event ' . $this->event->title . ' has been added by ' . $this->user->renderFullname(),
            'body' => '',
            'link' => route('event.show', $this->event->id),
            'linkLabel' => 'View Event',            
            'date' => date('c'),
        ];
    }
}
