<?php

namespace DanTheCoder\SaaSCore\Subscription\Exceptions;

class InvalidIntervalException extends \Exception
{
    /**
     * Create a new InvalidPlanFeatureException instance.
     *
     * @param $feature
     * @return void
     */
    public function __construct($interval)
    {
        $this->message = "Invalid interval \"{$interval}\".";
    }
}
