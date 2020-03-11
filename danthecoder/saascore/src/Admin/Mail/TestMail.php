<?php

namespace DanTheCoder\SaaSCore\Admin\Mail;

use Illuminate\Mail\Mailable;

class TestMail extends Mailable
{

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Test Mail')
                ->markdown('saascore::emails.test-mail');
    }
}
