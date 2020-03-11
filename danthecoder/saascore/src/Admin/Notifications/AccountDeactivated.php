<?php

namespace DanTheCoder\SaaSCore\Admin\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class AccountDeactivated extends Notification
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
            ->subject('Account Deactivated')
            ->greeting('Hey '. $notifiable->name . ',')
            ->line('Your account has been deactivated by our team and access to **' . config('app.name') . '** has been revoked.')
            ->line('If you believe this was done in error or have any concerns please feel free to contact our support team at ['. config('settings.support_email') .']('. config('settings.support_email') .') for further assistance.');
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
