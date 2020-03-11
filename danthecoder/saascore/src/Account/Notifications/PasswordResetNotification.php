<?php

namespace DanTheCoder\SaaSCore\Account\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Auth\Notifications\ResetPassword as ResetPassword;

class PasswordResetNotification extends ResetPassword
{

    /**
     * Custom password reset template
     * to override Laravel default template
     */
    public function toMail($notifiable)
    {
        if (static::$toMailCallback) {
            return call_user_func(static::$toMailCallback, $notifiable, $this->token);
        }

        return (new MailMessage)
            ->subject('Password Reset')
            ->greeting('Hey '. $notifiable->name . ',')
            ->line('You recently requested to reset your password for your '. config('app.name') .' account. Use the button below to reset it, this password reset link is only valid for **1 hour**.')
            ->action('Reset Password', url(config('app.url').route('password.reset', $this->token, false)))
            ->line('If you did not request a password reset, please ignore this email or if you have any questions contact support at ['. config('settings.support_email') .']('. config('settings.support_email') .').');
    }
}