<?php

namespace App\Notifications\Learnings;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class IDPHasApprover extends Notification implements ShouldQueue
{
    use Queueable;

    protected $idp;
    protected $idpApprover;


    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($idp, $idpApprover)
    {
        $this->idp = $idp;
        $this->idpApprover = $idpApprover;
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
                    ->subject('New IDP from ' . $this->idp->employee->renderFullname())
                    ->line('')  
                    ->line('IDP has been added:')
                    ->line('Assigned to ' . $this->idpApprover->approver->renderFullname())
                    ->line('')
                    ->line('For more details you may also view the request by clicking the button below.')
                    ->action('View', route('idp.show', $this->idp->id));
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
            'classes' => 'fa-book bg-blue',
            'text' => 'The IDP for ' . $this->idp->employee->renderFullname() . ' needs your approval',
            'body' => $this->idp->details,
            'link' => route('idp.show', $this->idp->id),
            'linkLabel' => 'View IDP',
            'date' => date('c'),
        ];
    }
}
