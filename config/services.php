<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, SparkPost and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'ses' => [
        'key' => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => env('SES_REGION', 'us-east-1'),
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'model' => App\Models\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],

    'paypal' => [
        'sandbox_client_id' => env('PAYPAL_SANDBOX_CLIENT_ID', ''),
        'sandbox_secret' => env('PAYPAL_SANDBOX_SECRET', ''),
        
        'live_client_id' => env('PAYPAL_LIVE_CLIENT_ID', ''),
        'live_secret' => env('PAYPAL_LIVE_SECRET', ''),

        'settings' => [

            /** 
             * Payment Mode
             *
             * Available options are 'sandbox' or 'live'
             */
            'mode' => env('PAYPAL_MODE', 'sandbox'),
            
            // Specify the max connection attempt (3000 = 3 seconds)
            'http.ConnectionTimeOut' => 3000,
           
            // Specify whether or not we want to store logs
            'log.LogEnabled' => true,
            
            // Specify the location for our PayPal logs
            'log.FileName' => storage_path() . '/logs/paypal.log',
            
            /** 
             * Log Level
             *
             * Available options: 'DEBUG', 'INFO', 'WARN' or 'ERROR'
             * 
             * Logging is most verbose in the DEBUG level and decreases 
             * as you proceed towards ERROR. WARN or ERROR would be a 
             * recommended option for live environments.
             * 
             */
            'log.LogLevel' => 'ERROR'
        ]
    ],

];
