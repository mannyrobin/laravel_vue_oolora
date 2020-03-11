<?php

namespace DanTheCoder\SaaSCore\Subscription\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class SubscriptionCanceled extends Notification
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
            ->subject('Important: Your membership has been paused')
            ->greeting('Hey '. $notifiable->name . ',')
            ->line('We were unable to process your subscription payment, so unfortunately we have paused your membership for now.')
            ->line("Obviously, we'd love to have you back. Simply click the button below to update your details and continue using our service.")
            ->action('Re-activate Membership', url('billing/plans'))
            ->line('If you feel this is a mistake on our end, please contact our support team for assistance at ['. config('settings.support_email') .']('. config('settings.support_email') .')' );
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
