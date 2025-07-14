<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);

        $permissions = Permission::all();
        $superAdminRole->syncPermissions($permissions);

        $userSuper = User::create([
            'username' => 'divo',
            'name' => 'divo',
            'email' => 'divo@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $userSuper->assignRole($superAdminRole);
    }
}
