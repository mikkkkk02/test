<?php

namespace App\Custom;

use Illuminate\Notifications\Messages\SimpleMessage;
use Illuminate\Notifications\Action;

class RequestMessage extends SimpleMessage
{
    /**
     * The request approval URL
     *
     * @var string|null
     */
    public $approve;

    /**
     * The request disapproval URL
     *
     * @var string|null
     */
    public $disapprove;    

    /**
     * Set the content of the approval URL.
     *
     * @param string $greeting
     *
     * @return $this
     */
    public function approve($approve)
    {
        $this->approve = $approve;

        return $this;
    }

    /**
     * Set the content of the disapproval URL.
     *
     * @param string $greeting
     *
     * @return $this
     */
    public function disapprove($disapprove)
    {
        $this->disapprove = $disapprove;

        return $this;        
    }

    /**
     * Get an array representation of the message.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'level' => $this->level,
            'subject' => $this->subject,
            'greeting' => $this->greeting,
            'salutation' => $this->salutation,
            'introLines' => $this->introLines,
            'outroLines' => $this->outroLines,
            'actionText' => $this->actionText,
            'actionUrl' => $this->actionUrl,
            'approve' => $this->approve,
            'disapprove' => $this->disapprove
        ];
    }
}
