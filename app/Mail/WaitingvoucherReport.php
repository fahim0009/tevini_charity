<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WaitingvoucherReport extends Mailable
{
    use Queueable, SerializesModels;
    public $array;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($array)
    {
        $this->array = $array;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('info@tevini.co.uk', 'Tevini.co.uk')
                    ->replyTo($this->array['cc'], 'Tevini')
                    ->subject('Waiting voucher Report')
                    ->attach($this->array['file'],['as'=>$this->array['file_name'], 'mime'=>'application/pdf'])
                    ->markdown('mail.waiting_voucher');
    }
}
