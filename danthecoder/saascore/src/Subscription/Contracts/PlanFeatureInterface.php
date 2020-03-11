<?php

namespace DanTheCoder\SaaSCore\Subscription\Contracts;

interface PlanFeatureInterface
{
    public function plan();
    public function usage();
}
