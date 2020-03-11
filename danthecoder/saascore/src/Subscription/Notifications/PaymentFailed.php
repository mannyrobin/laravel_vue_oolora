<?php

namespace DanTheCoder\SaaSCore\Subscription\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class PaymentFailed extends Notification
{

    protected $payment;


    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($payment)
    {
        $this->payment = $payment;
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
            ->subject( ( is_null($this->payment['next_attempt']) ? 'Final Notice:' : 'Important:') .' Subscription Renewal Failed')
            ->markdown('saascore::emails.payment-failed', ['payment' => $this->payment]);
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
            'title'     => ( is_null($this->payment['next_attempt']) ? 'Final Notice: Failed Payment' : 'Failed Subscription Payment'),
            'message'   => ( is_null($this->payment['next_attempt']) ? 'Please update your payment details to avoid any service interruptions.' : 'Your subscription renewal could not be processed, please update your payment details.'),
            'action'    => url('billing'),
        ];
    }
}
