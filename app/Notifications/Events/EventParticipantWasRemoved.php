<?php

namespace App\Notifications\Events;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class EventParticipantWasRemoved extends Notification implements ShouldQueue
{
    use Queueable;

    protected $event;
    protected $user;
    protected $isUser;


    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($event, $user, $isUser)
    {
        $this->event = $event;
        $this->user = $user;
        $this->isUser = $isUser;
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
                            ->subject('Your request to attend the Event ' . $this->event->title . ' has been updated!')
                            ->line('')
                            ->line('Event participation has been updated:')
                            ->line('Removed to the event')
                            ->line('')
                            ->line('For more details you may also view the request by clicking the button below.')
                            ->action('View', route('event.show', $this->event->id));

            break;
            case 0:

                return (new MailMessage)
                            ->subject('Request to attend the Event ' . $this->event->title .  ' of ' . $this->user->renderFullname() . ' has been updated!')
                            ->line('')
                            ->line('Event participation has been updated:')
                            ->line('Removed to the event')
                            ->line('')
                            ->line('For more details you may also view the request by clicking the button below.')
                            ->action('View', route('event.show', $this->event->id));

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
            'classes' => 'fa-calendar bg-red',
            'text' => $this->isUser ? 
                        'You have been removed as a participant to the event ' . $this->event->title:
                        $this->user->renderFullname() . ' has been removed as a participant to the event ' . $this->event->title,
            'body' => $this->isUser ? 
                        '' :
                        '',                        
            'link' => route('event.show', $this->event->id),
            'linkLabel' => 'View Event',
            'date' => date('c'),
        ];
    }
}
