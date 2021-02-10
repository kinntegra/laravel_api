<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SupervisiorNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $data;
    public $name;
    public $address;
    public $subject;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data, $subject)
    {
        $this->data = $data;
        $this->name = env('APP_NAME');
        $this->address = env('APP_EMAIL');
        $this->subject = $subject;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

       // dd($this->data);
        return $this->view('emails.supervisior')
                    ->from($this->address, $this->name)
                    //->cc($address, $name)
                    //->bcc($address, $name)
                    //->replyTo($address, $name)
                    ->subject($this->subject)
                    ->with(['data' => $this->data]);
    }
}
