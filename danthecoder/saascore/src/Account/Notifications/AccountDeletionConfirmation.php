<?php

namespace DanTheCoder\SaaSCore\Account\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class AccountDeletionConfirmation extends Notification
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
        return ['mail'];
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
            ->subject('Account Deletion Confirmation')
            ->greeting('Hey ' . $notifiable->name . ',')
            ->line("This message confirms that your ". config('app.name') ." account has been closed. ")
            ->line("If you didn't request to close your account, please contact support at [". config('settings.support_email') ."](". config('settings.support_email') .") to have your account restored.");
    }


    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [];
    }
}
