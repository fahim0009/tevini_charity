<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CampaignReport extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $array;
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
        return $this->view($this->array['view'])
        ->from($this->array['from'],'Tevini')
        ->cc($this->array['cc'])
        ->replyTo($this->array['cc'], 'Tevini')
        ->subject($this->array['subject'])
        ->attach($this->array['file'],['as'=>$this->array['file_name'], 'mime'=>'application/pdf'])
        ->markdown('mail.campaignreport');
    }
}
