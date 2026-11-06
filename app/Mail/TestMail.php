<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class TestMail extends Mailable
{
    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function build()
    {
        return $this->from(config('mail.from.address'), 'Ali Raza')
                    ->subject('Laravel Basic Testing Mail')
                    ->view('mail')
                    ->with('data', $this->data);
    }
}