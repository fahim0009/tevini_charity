<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\UserDetail;

class VerifyEmailMail extends Mailable
{
    use Queueable, SerializesModels;


    public $data;

    public function __construct(UserDetail $data)
    {
        $this->data = $data;
    }

    public function build()
    {
        return $this->subject('Verify Your Email Address')
                    ->markdown('mail.verify-email');
    }
}
