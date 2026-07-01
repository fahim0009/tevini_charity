<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VoucherDeclinedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $array;

    /**
     * Create a new message instance.
     */
    public function __construct(array $array)
    {
        $this->array = $array;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->from($this->array['from'], 'Tevini')
            ->subject($this->array['subject'])
            ->view('mail.voucher-declined');
    }
}