<?php

namespace App\Notifications\MrReservations;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class AssignedRoom extends Notification implements ShouldQueue
{
    use Queueable;

    protected $ticket;
    protected $user;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($ticket, $user)
    {
        $this->ticket = $ticket;
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
                    ->subject('Your request for Meeting Room Reservation' . $this->ticket->form->template->name . ' (Ticket No. ' . $this->ticket->id . ') has been updated!')
                    ->line('')
                    ->line('Request has been updated by ' . $this->user->renderFullname() . ':')
                    ->line('')
                    ->line('For more details you may also view the request by clicking the button below.')
                    ->action('View', route('ticket.show', $this->ticket->id));
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
            'classes' => 'fa-ticket bg-aqua',
            'text' => 'Your request for the ' . $this->ticket->form->template->name . ' (Ticket No. ' . $this->ticket->id . ') has been updated by ' . $this->user->renderFullname(),
            'link' => route('ticket.show', $this->ticket->id),
            'linkLabel' => 'View Ticket',
            'date' => date('c'),
        ];
    }
}
