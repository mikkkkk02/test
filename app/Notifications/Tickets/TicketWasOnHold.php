<?php

namespace App\Notifications\Tickets;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class TicketWasOnHold extends Notification implements ShouldQueue
{
    use Queueable;

    protected $ticket;

    protected $owner;
    protected $update;


    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($ticket, $update)
    {
        $this->ticket = $ticket;

        $this->owner = $ticket->owner;
        $this->update = $update;
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
                    ->subject('Your request for ' . $this->ticket->form->template->name . ' (Ticket No. ' . $this->ticket->id . ') has been updated!')
                    ->line('')
                    ->line('Request has been updated:')
                    ->line('On-hold by ' . $this->update->employee->renderFullname())
                    ->line('')
                    ->line($this->update->description)
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
            'classes' => 'fa-ticket bg-yellow',
            'text' => 'Your request for ' . $this->ticket->form->template->name . ' (Ticket No. ' . $this->ticket->id . ') has now been on-hold!',
            'body' => '',
            'link' => route('ticket.show', $this->ticket->id),
            'linkLabel' => 'View Ticket',
            'date' => date('c'),
        ];
    }
}
