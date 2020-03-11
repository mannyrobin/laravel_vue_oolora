<?php

namespace DanTheCoder\SaaSCore\Subscription\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class SubscriptionCancelRequest extends Notification
{

    protected $subscription;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($subscription)
    {
        $this->subscription = $subscription;
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
            ->subject('Subscription Cancel Request')
            ->greeting('Hey '. $notifiable->name . ',')
            ->line('You recently requested to have your membership subscription canceled. This request has been completed, your subscription will not be automatically renewed and membership access will end on **' . $this->subscription['ends_at'] . '**')
            ->line('If you did not request to cancel your membership, please contact our support team for assistance at ['. config('settings.support_email') .']('. config('settings.support_email') .')' );
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
            'title'     => 'Subscription Cancel Request',
            'message'   => 'Your subscription will not be automatically renewed and membership access will end on ' . $this->subscription['ends_at'],
            'action'    => url('billing'),
        ];
    }
}
