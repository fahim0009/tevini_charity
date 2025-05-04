<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactUsMail extends Mailable
{
    use Queueable, SerializesModels;

    public $name;
    public $email;
    public $visitor_subject;
    public $visitor_message;

    public function __construct($name, $email, $visitor_subject, $visitor_message)
    {
        $this->name = $name;
        $this->email = $email;
        $this->visitor_subject = $visitor_subject;
        $this->visitor_message = $visitor_message;
    }

    public function build()
    {
        return $this->subject("New Message from Tevini")
                    ->replyTo($this->email)
                    ->view('mail.contactmail');
    }
}
