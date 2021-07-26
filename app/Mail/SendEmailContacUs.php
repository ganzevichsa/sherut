<?php

namespace App\Mail;

use App\ContactUs;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendEmailContacUs extends Mailable
{
    use Queueable, SerializesModels;


    /**
     * @var ContactUs
     */
    public $data;

    /**
     * SendEmailContacUs constructor.
     * @param ContactUs $data
     */
    public function __construct(ContactUs $data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('email.contactus');
    }
}
