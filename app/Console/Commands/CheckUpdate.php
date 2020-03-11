<?php

namespace App\Console\Commands;

use Cache;
use Illuminate\Console\Command;
use DanTheCoder\SaaSCore\Admin\Models\Setting;

class CheckUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for new updates';

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
        // Get the latest version
        $getResult = file_get_contents('http://update.helpcommerce.com?software=' . config('settings.software.id') );
        $result = json_decode($getResult);


        // If latest version from HelpCommerce is the same as the application latest in the DB
        // Don't run the settings update
        if ( config('settings.software.latest_version') == $result->lastest_version )
            return;


        // Compare the version
        if ( $result->lastest_version > config('settings.software.version') ) {

            // Update latest version in the DB
            Setting::updateOrCreate(
                ['key'      => 'settings.software.latest_version'],
                ['value'    => $result->lastest_version]
            );


            // Flush settings cache
            Cache::flush('settings');
        }
    }
}