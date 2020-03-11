<?php 

namespace DanTheCoder\SaaSCore\Helpers;

use Carbon\Carbon;

class Helper
{

    /**
     * Format dates based on admin settings
     *
     * @param  string  $date
     * @return string
     */
    static function formatDate($date)
    {
        return Carbon::parse($date)->timezone( auth()->user()->timezone )->format( config('settings.date_format') );
    }


    /**
     * Guess the currency symbol for the given currency.
     *
     * @param  string  $currency
     * @return string
     */
    static function guessCurrencySymbol($currency)
    {
        switch (strtolower($currency)) {
            case 'usd':
            case 'aud':
            case 'cad':
                return '$';
            case 'eur':
                return '€';
            case 'gbp':
                return '£';
            default:
                return '';
        }
    }


    /**
     * Get the icon name that should be use for the notification
     *
     * @param  string  $notificationType
     * @return string
     */
    static function getNotificationIcon($notificationType)
    {
        switch ($notificationType) {
            case 'DanTheCoder\SaaSCore\Subscription\Notifications\PaymentSucceeded':
                return 'credit-card-front';

            case 'DanTheCoder\SaaSCore\Subscription\Notifications\PaymentFailed':
                return 'file-invoice-dollar';

            case 'DanTheCoder\SaaSCore\Subscription\Notifications\TrialEnding':
                return 'alarm-clock';

            case 'DanTheCoder\SaaSCore\Subscription\Notifications\SubscriptionCancelRequest':
                return 'user-slash';

            case 'DanTheCoder\SaaSCore\Subscription\Notifications\ReactivateSubscription':
                return 'user-shield';

            case 'DanTheCoder\SaaSCore\Account\Notifications\PasswordChangeConfirmation':
                return 'user-lock';

            default:
                return 'bells';
        }
    }


    /**
     * Get the notification icon color that should be use for the type of notification
     *
     * @param  string  $notificationType
     * @return string
     */
    static function getNotificationIconColor($notificationType)
    {
        switch ($notificationType) {
            case 'DanTheCoder\SaaSCore\Subscription\Notifications\PaymentSucceeded':
                return '#51D88A';

            case 'DanTheCoder\SaaSCore\Subscription\Notifications\PaymentFailed':
                return '#EF5753';

            case 'DanTheCoder\SaaSCore\Subscription\Notifications\TrialEnding':
                return '#F65846';

            case 'DanTheCoder\SaaSCore\Subscription\Notifications\SubscriptionCancelRequest':
                return '#FAAD63';

            case 'DanTheCoder\SaaSCore\Subscription\Notifications\ReactivateSubscription':
                return '#51D88A';
         
            case 'DanTheCoder\SaaSCore\Account\Notifications\PasswordChangeConfirmation':
                return '#64D5CA';

            default:
                return '#6CB2EB';
        }
    }

}