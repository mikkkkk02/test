<?php

namespace App\Notifications\Events;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class EventParticipantWasDisapproved extends Notification implements ShouldQueue
{
    use Queueable;

    protected $event;
    protected $user;
    protected $reason;


    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($event, $user, $reason)
    {
        $this->event = $event;
        $this->user = $user;
        $this->reason = $reason;
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
                    ->subject('Your request to attend the Event ' . $this->event->title . ' has been updated!')
                    ->line('')
                    ->line('Your request to attend the Event ' . $this->event->title . ' has been disapproved')
                    ->line('Reason:' . $this->reason)
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
            'classes' => 'fa-calendar bg-red',            
            'text' => 'Your request to attend the event ' . $this->event->title . ' has been disapproved',
            'body' => 'Reason: ' . $this->reason,
            'link' => route('event.show', $this->event->id),
            'linkLabel' => 'View Event',            
            'date' => date('c'),
        ];
    }
}
