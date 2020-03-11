<?php

namespace DanTheCoder\SaaSCore\Account\Listeners;

use Illuminate\Auth\Events\PasswordReset;
use DanTheCoder\SaaSCore\Account\Notifications\PasswordChangeConfirmation;

class SendPasswordChangeConfirmation
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }


    /**
     * Send a confirmation email and system notification upon successful password changed
     *
     * @param  PasswordReset $event
     * @return void
     */
    public function handle(PasswordReset $event)
    {   
        // Send the notification
        $event->user->notify( new PasswordChangeConfirmation() );
    }
}
