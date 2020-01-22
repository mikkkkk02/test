<?php

namespace App\Notifications\Learnings;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class IDPWasUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    protected $idp;
    protected $user;
    protected $isUser;

    protected $tempIDP;


    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($idp, $idpTemp, $user, $isUser)
    {
        $this->idp = $idp;
        $this->user = $user;
        $this->isUser = $isUser;

        $this->tempIDP = $idpTemp;
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
                            ->subject('Your IDP No.' . $this->idp->id . ' has been updated!')
                            ->line('')                    
                            ->line('Your IDP has been updated:')
                            ->line('Awaiting approval of changes')
                            ->line('')
                            ->line('For more details you may also view the updates by clicking the button below.')
                            ->action('View', route('idptmp.show', $this->tempIDP->id));

            break;
            case 0:

                return (new MailMessage)
                            ->subject('New IDP No.' . $this->idp->id . ' has been updated!')
                            ->line('')                    
                            ->line('IDP has been updated:')
                            ->line('Awaiting approval of changes')
                            ->line('')
                            ->line('For more details you may also view the updates by clicking the button below.')
                            ->action('View', route('idptmp.show', $this->tempIDP->id));                            

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
            'classes' => 'fa-book bg-yellow',
            'text' => $this->isUser ? 
                        'Your changes to IDP No.' . $this->idp->id . ' has been sent for approval' :
                        $this->user->renderFullname() . ' has updated IDP No.' . $this->idp->id . ' and needs your approval',
            'body' => $this->isUser ? 
                        '' :
                        '', 
            'link' => route('idptmp.show', $this->tempIDP->id),
            'linkLabel' => 'View IDP',
            'date' => date('c'),
        ];
    }
}
