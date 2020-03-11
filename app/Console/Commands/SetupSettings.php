<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class SetupSettings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setup:settings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Insert the default settings data to the database';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {   
        // Flush all cache
        Cache::flush();

        DB::table('settings')->insert([
            [
                'key'           => 'app.name',
                'value'         => env('APP_NAME', 'My WebApp')
            ],[
                'key'           => 'app.url',
                'value'         => env('APP_URL', 'http://localhost'),
            ],[
                'key'           => 'app.timezone',
                'value'         => 'UTC'
            ],


            [
                'key'           => 'services.paypal.settings.mode',
                'value'         => ''
            ],[
                'key'           => 'services.paypal.sandbox_client_id',
                'value'         => ''
            ],[
                'key'           => 'services.paypal.sandbox_secret',
                'value'         => ''
            ],[
                'key'           => 'services.paypal.live_client_id',
                'value'         => ''
            ],[
                'key'           => 'services.paypal.live_secret',
                'value'         => ''
            ],[
                'key'           => 'services.paypal.enable',
                'value'         => 0
            ],


            [
                'key'           => 'services.stripe.key',
                'value'         => ''
            ],[
                'key'           => 'services.stripe.secret',
                'value'         => ''
            ],[
                'key'           => 'services.stripe.enable',
                'value'         => 0
            ],


            [
                'key'           => 'mail.driver',
                'value'         => ''
            ],[
                'key'           => 'mail.host',
                'value'         => ''
            ],[
                'key'           => 'mail.port',
                'value'         => ''
            ],[
                'key'           => 'mail.from.address',
                'value'         => ''
            ],[
                'key'           => 'mail.from.name',
                'value'         => ''
            ],[
                'key'           => 'mail.username',
                'value'         => ''
            ],[
                'key'           => 'mail.password',
                'value'         => ''
            ],


            [
                'key'           => 'settings.logo_light',
                'value'         => ''
            ],[
                'key'           => 'settings.logo_dark',
                'value'         => ''
            ],[
                'key'           => 'settings.favicon',
                'value'         => ''
            ],[
                'key'           => 'settings.currency_code',
                'value'         => 'USD'
            ],[
                'key'           => 'settings.currency_symbol',
                'value'         => '$'
            ],[
                'key'           => 'settings.date_format',
                'value'         => 'M d, Y'
            ],[
                'key'           => 'settings.invoice_prefix',
                'value'         => 'INV'
            ],[
                'key'           => 'settings.support_email',
                'value'         => ''
            ]
        ]);

    }
}