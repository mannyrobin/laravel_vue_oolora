<?php

namespace DanTheCoder\SaaSCore\Admin\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class PaymentRefunded extends Notification
{

    protected $refund;


    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($refund)
    {
        $this->refund = $refund;
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
            ->subject('Payment Refunded')
            ->greeting('Hey '. $notifiable->name . ',')
            ->line('A refund of **' . $this->refund['currency_symbol'] . $this->refund['refund_amount'] . '** for your subscription payment was issued. ' . ($this->refund['cancel_subscription'] ? 'Your membership subscription was also canceled due to the refund.' : '') )
            ->line('This amount will be reflected in your Bank/PayPal account within a few business days.')
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
            'title'     => 'Payment Refunded',
            'message'   => 'A refund of ' . $this->refund['currency_symbol'] . $this->refund['refund_amount'] . ' for your subscription payment was issued.',
            'action'    => url('billing'),
        ];
    }
}
