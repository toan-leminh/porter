<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class GeneralMail extends Mailable
{
    use Queueable, SerializesModels;

    private $template;
    private $options;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($template, $subject = '', $data = [], $options = [])
    {
        $this->viewData = $data;
        $this->subject = $subject;

        $this->template = $template;
        $this->options = $options;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // Create mail
        $mail =  $this->view('email.' . $this->template)
        ;
        if(isset($this->options['from'])){
            $mail->from($this->options['from']);
        }
        if(isset($this->options['cc'])){
            $mail->cc($this->options['cc']);
        }
        if(isset($this->options['bcc'])){
            $mail->bcc($this->options['bcc']);
        }

        return $mail;
    }
}
