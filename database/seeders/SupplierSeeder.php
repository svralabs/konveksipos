<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Supplier;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = [
            ['name' => 'PT Textile Indo', 'phone' => '081234567801', 'address' => 'Bandung'],
            ['name' => 'Garment Supply Co.', 'phone' => '081234567802', 'address' => 'Jakarta'],
            ['name' => 'Distributor Benang Astra', 'phone' => '081234567803', 'address' => 'Tangerang'],
            ['name' => 'Pabrik Resleting YKK', 'phone' => '081234567804', 'address' => 'Bekasi'],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::firstOrCreate(['name' => $supplier['name']], $supplier);
        }
    }
}
