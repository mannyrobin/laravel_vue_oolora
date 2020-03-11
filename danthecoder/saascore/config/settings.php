<?php

return [
    // Set subscription plans features
    'plan_features' => [
        'SAMPLE_SIMPLE_FEATURE',
        'SAMPLE_DEFINED_FEATURE' => [
            'resettable_interval' => 'month',
            'resettable_count' => 2
        ],
    ],


    // Subscription Positive Words
    // These words indicates "true" and are used to check if a particular plan feature is enabled.
    'positive_words' => [
        'UNLIMITED',
    ],
];