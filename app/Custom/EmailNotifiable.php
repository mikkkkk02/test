<?php

namespace App\Custom;

use \Illuminate\Notifications\Notifiable;

class EmailNotifiable
{
	use Notifiable;

    public $email;

    public function __construct($email)
    {
        $this->email = $email;
    }
}