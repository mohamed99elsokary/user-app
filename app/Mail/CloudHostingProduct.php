<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CloudHostingProduct extends Mailable
{
    use Queueable, SerializesModels;
    public $name;

    public function __construct($name)
    {
        $this->name = $name;
    }
    public function build()
    {
        $address = 'joker171joker72@gmail.com';
        $subject = 'This is a demo!';
        $name = 'Jane Doe';

        return $this->view('email_template')
            ->from($address, $name)
            ->subject($subject);
    }
}
