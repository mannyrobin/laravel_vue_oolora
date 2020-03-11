<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class SetupPermission extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setup:permission';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup all the permission that the app require and assign admin role to the first user';

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
        // Create role and the permissions
        $role = Role::create(['name' => 'Administrator']);
        $permission = Permission::create(['name' => 'access admin']);


        // Assign permission to role
        $role->givePermissionTo($permission);


        // Assign admin role to a user
        $user = User::find(1);
        $user->assignRole('Administrator');

    }
}