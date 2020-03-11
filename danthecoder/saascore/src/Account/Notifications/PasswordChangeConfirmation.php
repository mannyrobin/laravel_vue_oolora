<?php

namespace DanTheCoder\SaaSCore\Account\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class PasswordChangeConfirmation extends Notification
{


    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }


    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }


    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Password Reset Confirmation')
            ->greeting('Hey ' . $notifiable->name . ',')
            ->line('This message is to confirm that your ' . config('app.name') .' account password has been successfully changed.')
            ->line('If you did not change your password and believe that an unauthorized person has accessed your account, you can reset your password by [Clicking Here](' . route('password.request') . ').')
            ->line('If you have any questions, please contact our support team for assistance at ['. config('settings.support_email') .']('. config('settings.support_email') .')');
    }


    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'title'     => 'Password Changed',
            'message'   => 'Your account password has been changed.',
            'action'    => url('account'),
        ];
    }
}
