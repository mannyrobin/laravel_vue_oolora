<?php

namespace DanTheCoder\SaaSCore\Subscription\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ReactivateSubscription extends Notification
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
            ->subject('Subscription Reactivated')
            ->greeting('Hey '. $notifiable->name . ',')
            ->line('This message is to confirm that your membership subscription has been reactivated.')
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
            'title'     => 'Subscription Reactivated',
            'message'   => 'Your membership subscription has been reactivated.',
            'action'    => url('billing'),
        ];
    }
}
