<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'admin',
            ],[
                'name' => 'staff',
            ]
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate($role);
        }

        $permissions = [
            [
                'name' => 'create_customer',
            ],[
                'name' => 'edit_customer',
            ],[
                'name' => 'view_customer',
            ],[
                'name' => 'delete_customer',
            ]
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate($permission);
        }

        $admin = Role::findByName('admin');
        $admin->givePermissionTo(['create_customer', 'edit_customer', 'view_customer', 'delete_customer']);

        $staff = Role::findByName('staff');
        $staff->givePermissionTo(['view_customer']);


        $adminUser = User::where('email', 'admin@example.com')->first();
        $adminUser->assignRole('admin');

        $staffUser = User::where('email', 'staff@example.com')->first();
        $staffUser->assignRole('staff');
    }
}
