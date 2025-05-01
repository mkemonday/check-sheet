<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

class SendEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-emails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // $user = User::find(2);
        // $role = Role::findByName('admin');
        // $user->assignRole($role);

        $array = [
            'create',
            'edit',
            'delete',
            'view',
        ];

        foreach ($array as $permission) {

            $modules = [
                'users',
                'roles',
                'permissions',
                'check-method',
                'check-item',
                'daily-check',
            ];
            foreach ($modules as $module) {
                $permissionName = $permission . '-' . $module;
                // Check if the permission already exists
                if (!\Spatie\Permission\Models\Permission::where('name', $permissionName)->exists()) {
                    // Create the permission
                    \Spatie\Permission\Models\Permission::create(['name' => $permissionName]);
                }
            }
        }
        $this->info('Permissions assigned to admin role successfully.');
    }
}
