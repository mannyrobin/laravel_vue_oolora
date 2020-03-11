<?php

namespace DanTheCoder\SaaSCore\Subscription\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class TrialEnding extends Notification
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
            ->subject('Your Free Trial Is Ending Soon')
            ->greeting('Hey '. $notifiable->name . ',')
            ->line('Thanks for signing up for our service. We hope you have been enjoying your free trial so far!')
            ->line('**Unfortunately, your free trial will expire on '. $this->subscription['trial_expiration'] . '.**')
            ->line("We'd love to have you as a customer, and there is still time to complete your subscription enrollment. Simply click on the button below to subscribe.")
            ->action('Upgrade Your Subscription', url('billing/plans'))
            ->line('If you have any questions, please contact our support team for assistance at ['. config('settings.support_email') .']('. config('settings.support_email') .')' );
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
            'title'     => 'Free Trial Ending Soon',
            'message'   => 'Your free trial will expire on '. $this->subscription['trial_expiration'] .', upgrade your subscription now.',
            'action'    => url('billing/plans'),
        ];
    }
}
