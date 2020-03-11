<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Subscription Plan Features
    |--------------------------------------------------------------------------
    |
    | All the features available for the plans
    |
    */
    'plan_features' => [
        'clicks' => [
            'name'          => 'Total Clicks',
            'value'         => 20000,
            'selection'     => null,
            'sort_order'    => 1
        ],

        'campaigns' => [
            'name'          => 'Campaigns',
            'value'         => 'UNLIMITED',
            'selection'     => null,
            'sort_order'    => 2,
        ],

        'links' => [
            'name'          => 'Links',
            'value'         => 100,
            'selection'     => null,
            'sort_order'    => 3,
        ],

        'call_to_actions' => [
            'name'          => 'Call to Actions',
            'value'         => 5,
            'selection'     => null,
            'sort_order'    => 4,
        ],

        'pixels' => [
            'name'          => 'Pixels',
            'value'         => 10,
            'selection'     => null,
            'sort_order'    => 5,
        ],


        'custom_scripts' => [
            'name'          => 'Custom Scripts',
            'value'         => 5,
            'selection'     => null,
            'sort_order'    => 6,
        ],

        'domains' => [
            'name'          => 'Domains',
            'value'         => 3,
            'selection'     => null,
            'sort_order'    => 7,
        ],

        'analytics' => [
            'name'          => 'Analytics',
            'value'         => 'INCLUDE',
            'selection'     => 1,
            'sort_order'    => 8,
        ]
    ],


    /*
    |--------------------------------------------------------------------------
    | Subscription Positive Words
    |--------------------------------------------------------------------------
    |
    | These words indicates "true" and are used to check if a particular plan
    | feature is enabled.
    |
    */
    'positive_words' => [
        'INCLUDE',
        'UNLIMITED',
    ],


    /**
     * Information about the application used for updates
     * version will be updated and save it the DB when the system check
     * as well as the latest_version
     */
    'software' => [
        'id'                => 1,
        'version'           => '1.0.3',
        'latest_version'    => '1.0.3'
    ],

];