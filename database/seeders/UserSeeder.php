<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // First, check if Role model class exists before using Spatie Permission
        $hasSpatie = class_exists(Role::class);
        
        if ($hasSpatie) {
            Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
            Role::firstOrCreate(['name' => 'kasir', 'guard_name' => 'web']);
            Role::firstOrCreate(['name' => 'admin_gudang', 'guard_name' => 'web']);
        }

        $superAdmin = User::firstOrCreate([
            'email' => 'superadmin@konveksipos.com',
        ], [
            'name' => 'Super Admin',
            'password' => Hash::make('password'),
        ]);
        if ($hasSpatie) $superAdmin->assignRole('super_admin');

        $kasir = User::firstOrCreate([
            'email' => 'kasir@konveksipos.com',
        ], [
            'name' => 'Kasir Utama',
            'password' => Hash::make('password'),
        ]);
        if ($hasSpatie) $kasir->assignRole('kasir');

        $adminGudang = User::firstOrCreate([
            'email' => 'gudang@konveksipos.com',
        ], [
            'name' => 'Admin Gudang',
            'password' => Hash::make('password'),
        ]);
        if ($hasSpatie) $adminGudang->assignRole('admin_gudang');

        // Give initial modal/saldo to users for testing & seeding expenses
        foreach ([$superAdmin, $kasir, $adminGudang] as $u) {
            $u->deposit(50000000000, ['description' => 'Modal Awal Sistem']); // Rp 500.000.000
        }
    }
}

