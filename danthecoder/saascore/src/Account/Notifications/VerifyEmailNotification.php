<?php

namespace DanTheCoder\SaaSCore\Account\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Auth\Notifications\VerifyEmail as VerifyEmailBase;

class VerifyEmailNotification extends VerifyEmailBase
{

    /**
     * Custom email verification template
     * to override Laravel default template
     */
    public function toMail($notifiable)
    {
        if (static::$toMailCallback) {
            return call_user_func(static::$toMailCallback, $notifiable);
        }

        return (new MailMessage)
            ->subject('Welcome to ' . config('app.name'))
            ->greeting('Hey '. $notifiable->name . ',')
            ->line("We're absolutely thrilled that you've decided to join ". config('app.name') ."! To get the most out of ". config('app.name') .", please click the button below to verify your email address.")
            ->action( 'Verify Email Address', $this->verificationUrl($notifiable) )
            ->line('If you did not create an account, no further action is required.');
    }
}