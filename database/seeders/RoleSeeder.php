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

        // Create standard roles
        $superadmin  = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $kasir       = Role::firstOrCreate(['name' => 'kasir', 'guard_name' => 'web']);
        $adminGudang = Role::firstOrCreate(['name' => 'admin_gudang', 'guard_name' => 'web']);

        // Give initial default permissions to Kasir role
        $kasirPermissions = [
            'View:PosKasir',
            'ViewAny:Order',
            'View:Order',
            'Create:Order',
            'ViewAny:CashRegister',
            'View:CashRegister',
            'ViewAny:Customer',
            'View:Customer',
            'Create:Customer',
            'ViewAny:Product',
            'View:Product',
        ];

        foreach ($kasirPermissions as $perm) {
            $permissionModel = Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
            $kasir->givePermissionTo($permissionModel);
        }

        // Give initial default permissions to Admin Gudang role
        $gudangPermissions = [
            'View:StockInPage',
            'ViewAny:Product',
            'View:Product',
            'Create:Product',
            'Update:Product',
            'ViewAny:StockAdjustment',
            'View:StockAdjustment',
            'Create:StockAdjustment',
            'ViewAny:Supplier',
            'View:Supplier',
            'ViewAny:Category',
            'View:Category',
            'ViewAny:Unit',
            'View:Unit',
        ];

        foreach ($gudangPermissions as $perm) {
            $permissionModel = Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
            $adminGudang->givePermissionTo($permissionModel);
        }

        // Assign users
        $adminUser = User::firstOrCreate(
            ['email' => 'superadmin@konveksipos.com'],
            [
                'name'     => 'Super Admin',
                'password' => Hash::make('password'),
            ]
        );
        $adminUser->syncRoles([$superadmin]);

        $kasirUser = User::firstOrCreate(
            ['email' => 'kasir@konveksipos.com'],
            [
                'name'     => 'Kasir Utama',
                'password' => Hash::make('password'),
            ]
        );
        $kasirUser->syncRoles([$kasir]);

        $gudangUser = User::firstOrCreate(
            ['email' => 'gudang@konveksipos.com'],
            [
                'name'     => 'Admin Gudang',
                'password' => Hash::make('password'),
            ]
        );
        $gudangUser->syncRoles([$adminGudang]);

        $this->command->info('Roles & Permissions seeded successfully!');
    }
}
