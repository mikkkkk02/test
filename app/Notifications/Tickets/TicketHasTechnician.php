<?php

namespace App\Notifications\Tickets;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class TicketHasTechnician extends Notification implements ShouldQueue
{
    use Queueable;

    protected $ticket;
    protected $isUser;

    protected $owner;
    protected $technician;


    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($ticket, $isUser)
    {
        $this->ticket = $ticket;
        $this->isUser = $isUser;

        $this->owner = $ticket->owner;
        $this->technician = $ticket->technician;
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
                            ->subject('Your request for ' . $this->ticket->form->template->name . ' (Ticket No. ' . $this->ticket->id . ') has been updated!')
                            ->line('')
                            ->line('Request has been updated:')
                            ->line('Assigned to ' . $this->technician->renderFullname())
                            ->line('')                            
                            ->line('For more details you may also view the request by clicking the button below.')
                            ->action('View', route('ticket.show', $this->ticket->id));

            break;
            case 0:

                return (new MailMessage)
                            ->subject('Request for ' . $this->ticket->form->template->name . ' (Ticket No. ' . $this->ticket->id . ') from ' . $this->owner->renderFullname() . ' has been updated!')
                            ->line('')
                            ->line('Request has been updated:')
                            ->line('Assigned to ' . $this->technician->renderFullname())
                            ->line('')
                            ->line('For more details you may also view the request by clicking the button below.')
                            ->action('View', route('ticket.show', $this->ticket->id));

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
            'classes' => 'fa-ticket bg-green',
            'text' => $this->isUser ? 
                        'Your request for ' . $this->ticket->form->template->name . ' (Ticket No. ' . $this->ticket->id . ') has been assigned to ' . $this->technician->renderFullname() :
                        'New request for ' . $this->ticket->form->template->name . ' (Ticket No. ' . $this->ticket->id . ') has been assigned to you',
            'body' => $this->isUser ? 
                        '' :
                        '',
            'link' => route('ticket.show', $this->ticket->id),
            'linkLabel' => 'View Ticket',
            'date' => date('c'),
        ];
    }
}
