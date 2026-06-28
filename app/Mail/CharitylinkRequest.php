<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CharitylinkRequest extends Mailable
{
    use Queueable, SerializesModels;
    public $array;
    
    public function __construct($array)
    {
        $this->array = $array;
    }

    public function build()
    {
        return $this->from('info@tevini.co.uk', 'Tevini.co.uk')
            ->replyTo($this->array['cc'], 'Tevini')
            ->subject('Donation Request from ' . $this->array['charity_name'])
            ->view('mail.charity_linkrequest');
    }
}