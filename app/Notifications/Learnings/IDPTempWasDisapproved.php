<?php

namespace App\Notifications\Learnings;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class IDPTempWasDisapproved extends Notification implements ShouldQueue
{
    use Queueable;

    protected $idp;
    protected $approver;


    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($idp, $approver)
    {
        $this->idp = $idp;
        $this->approver = $approver;
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
                    ->subject('Your IDP No.' . $this->idp->id . ' has been updated!')
                    ->line('')                    
                    ->line('Your IDP has been updated:')
                    ->line('Disapproved by ' . $this->approver->renderFullname())
                    ->line('')
                    ->line('For more details you may also view the updates by clicking the button below.')
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
            'classes' => 'fa-book bg-red',
            'text' => 'Your changes to IDP No.' . $this->idp->id . ' has been disapproved by ' . $this->approver->renderFullname(),
            'body' => '',
            'link' => route('idp.show', $this->idp->id),
            'linkLabel' => 'View IDP',            
            'date' => date('c'),
        ];
    }
}
