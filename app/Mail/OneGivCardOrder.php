<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OneGivCardOrder extends Mailable
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
                    ->replyTo('info@tevini.co.uk', 'Tevini')
                    ->subject('OneGiv Card Order Confirmation')
                    ->markdown('mail.onegiv-card-order');
    }
}