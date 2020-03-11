<?php

namespace DanTheCoder\SaaSCore\Subscription\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class TrialExpired extends Notification
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
            ->subject('Important: Your Free Trial Has Expired')
            ->greeting('Hey '. $notifiable->name . ',')
            ->line('Your free trial period has ended, but you can keep using our service with a paid subscription plan. Click the button below to get started.')
            ->action('Subscribe Now', url('billing/plans'))
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
        return [];
    }
}
