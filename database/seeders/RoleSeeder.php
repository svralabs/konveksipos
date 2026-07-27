<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create roles
        $superadmin = Role::firstOrCreate(['name' => 'superadmin', 'guard_name' => 'web']);
        $kasir      = Role::firstOrCreate(['name' => 'kasir', 'guard_name' => 'web']);
        $gudang     = Role::firstOrCreate(['name' => 'gudang', 'guard_name' => 'web']);

        // Create or update superadmin user
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@konveksipos.com'],
            [
                'name'     => 'Super Admin',
                'password' => Hash::make('password'),
            ]
        );
        $adminUser->syncRoles([$superadmin]);

        // Create sample kasir user
        $kasirUser = User::firstOrCreate(
            ['email' => 'kasir@konveksipos.com'],
            [
                'name'     => 'Kasir Utama',
                'password' => Hash::make('password'),
            ]
        );
        $kasirUser->syncRoles([$kasir]);

        // Create sample gudang user
        $gudangUser = User::firstOrCreate(
            ['email' => 'gudang@konveksipos.com'],
            [
                'name'     => 'Admin Gudang',
                'password' => Hash::make('password'),
            ]
        );
        $gudangUser->syncRoles([$gudang]);

        $this->command->info('Roles & Users seeded successfully!');
        $this->command->info('  superadmin -> admin@konveksipos.com / password');
        $this->command->info('  kasir      -> kasir@konveksipos.com / password');
        $this->command->info('  gudang     -> gudang@konveksipos.com / password');
    }
}
