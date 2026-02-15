<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CharityDailyReport extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $mailData;

    public function __construct($mailData)
    {
        $this->mailData = $mailData;
    }

    public function build()
    {
        return $this->view('mail.charity_daily_report')
                    ->subject($this->mailData['subject'])
                    ->attach($this->mailData['file'], [
                        'as' => 'Statement-' . $this->mailData['date'] . '.pdf',
                        'mime' => 'application/pdf',
                    ]);
    }
}