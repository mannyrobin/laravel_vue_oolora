<?php

namespace DanTheCoder\SaaSCore;

use Cache;
use Illuminate\Support\ServiceProvider;
use DanTheCoder\SaaSCore\Admin\Models\Setting;
use DanTheCoder\SaaSCore\Admin\Http\Middleware\CheckAdmin;
use DanTheCoder\SaaSCore\Subscription\Http\Middleware\CheckMembership;


// Subscription Module
use DanTheCoder\SaaSCore\Subscription\SubscriptionBuilder;
use DanTheCoder\SaaSCore\Subscription\SubscriptionResolver;
use DanTheCoder\SaaSCore\Subscription\Contracts\PlanInterface;
use DanTheCoder\SaaSCore\Subscription\Contracts\PlanFeatureInterface;
use DanTheCoder\SaaSCore\Subscription\Contracts\PlanSubscriptionInterface;
use DanTheCoder\SaaSCore\Subscription\Contracts\SubscriptionBuilderInterface;
use DanTheCoder\SaaSCore\Subscription\Contracts\SubscriptionResolverInterface;
use DanTheCoder\SaaSCore\Subscription\Contracts\PlanSubscriptionUsageInterface;


class SaaSCoreServiceProvider extends ServiceProvider
{

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {

        // Set website config settings
        $settings = Cache::rememberForever('settings', function() {
            return array_pluck(Setting::all()->toArray(), 'value', 'key');
        });
        config($settings);


        // Load Routes
        $this->loadRoutesFrom(__DIR__.'/routes.php');

        // Views
        $this->loadViewsFrom(__DIR__.'/views', 'saascore');

        // Migrations
        $this->loadMigrationsFrom(__DIR__.'/../migrations');


        // Publishing is only necessary when using the CLI.
        if ( $this->app->runningInConsole() ) {

            // Publishing the configuration file.
            $this->publishes([
                __DIR__.'/../config/settings.php' => config_path('settings.php'),
            ], 'saascore.config');


            // Publish views
            $this->publishes([
                __DIR__.'/views' => resource_path('views/vendor/saascore'),
            ], 'saascore.views');
        }
    }


    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/settings.php', 'settings');


        // Add the membership check middleware
        $this->app['router']->aliasMiddleware('membership' , CheckMembership::class);
        $this->app['router']->aliasMiddleware('admin' , CheckAdmin::class);


        // Subscription Module
        $this->app->bind(PlanInterface::class, 'DanTheCoder\SaaSCore\Subscription\Models\Plan');
        $this->app->bind(PlanFeatureInterface::class, 'DanTheCoder\SaaSCore\Subscription\Models\PlanFeature');
        $this->app->bind(PlanSubscriptionInterface::class, 'DanTheCoder\SaaSCore\Subscription\Models\PlanSubscription');
        $this->app->bind(PlanSubscriptionUsageInterface::class, 'DanTheCoder\SaaSCore\Subscription\Models\PlanSubscriptionUsage');
        $this->app->bind(SubscriptionBuilderInterface::class, SubscriptionBuilder::class);
        $this->app->bind(SubscriptionResolverInterface::class, SubscriptionResolver::class);
    }

}