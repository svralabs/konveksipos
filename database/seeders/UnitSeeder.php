<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Unit;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            ['name' => 'Roll', 'code' => 'roll'],
            ['name' => 'Meter', 'code' => 'mtr'],
            ['name' => 'Yard', 'code' => 'yd'],
            ['name' => 'Gross', 'code' => 'grs'],
            ['name' => 'Lusin', 'code' => 'lsn'],
            ['name' => 'Pieces', 'code' => 'pcs'],
            ['name' => 'Kilogram', 'code' => 'kg'],
            ['name' => 'Gram', 'code' => 'g'],
            ['name' => 'Kotak/Box', 'code' => 'box'],
        ];

        foreach ($units as $unit) {
            Unit::firstOrCreate(['code' => $unit['code']], $unit);
        }
    }
}
