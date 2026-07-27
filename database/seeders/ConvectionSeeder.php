<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Unit;
use App\Models\Product;

class ConvectionSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Categories
        $categories = [
            'Kain & Bahan Baku', 'Benang', 'Kancing & Resleting', 
            'Jarum Mesin & Tangan', 'Aksesoris & Peralatan', 'Sparepart & Perawatan Mesin'
        ];
        foreach ($categories as $cat) {
            Category::firstOrCreate(['name' => $cat]);
        }

        // 2. Seed Units (UoM)
        $units = [
            ['name' => 'Roll', 'code' => 'RLL'],
            ['name' => 'Kilogram', 'code' => 'KG'],
            ['name' => 'Meter', 'code' => 'MTR'],
            ['name' => 'Yard', 'code' => 'YRD'],
            ['name' => 'Cones', 'code' => 'CNS'],
            ['name' => 'Gross', 'code' => 'GRS'], // 144 pcs
            ['name' => 'Lusin', 'code' => 'LSN'], // 12 pcs
            ['name' => 'Pack', 'code' => 'PCK'],
            ['name' => 'Pcs', 'code' => 'PCS'],
            ['name' => 'Botol', 'code' => 'BTL'],
            ['name' => 'Box', 'code' => 'BOX'],
        ];
        foreach ($units as $unit) {
            Unit::firstOrCreate(['code' => $unit['code']], $unit);
        }

        // 3. Seed Products Dummy
        $products = [
            [
                'sku' => 'KAIN-001', 'name' => 'Kain Cotton Combed 30s - Hitam Reaktif',
                'category_id' => 1, 'unit_id' => 2, // Kg
                'cost_price' => 110000, 'selling_price' => 125000, 'stock' => 50
            ],
            [
                'sku' => 'BNG-001', 'name' => 'Benang Jahit Astra 5000 Yard - Putih',
                'category_id' => 2, 'unit_id' => 5, // Cones
                'cost_price' => 12500, 'selling_price' => 16000, 'stock' => 120
            ],
            [
                'sku' => 'RES-001', 'name' => 'Resleting YKK Jepang (Invisible) 25cm - Hitam',
                'category_id' => 3, 'unit_id' => 7, // Lusin
                'cost_price' => 30000, 'selling_price' => 42000, 'stock' => 30
            ],
            [
                'sku' => 'KNC-001', 'name' => 'Kancing Kemeja Bening 4 Lubang (11mm)',
                'category_id' => 3, 'unit_id' => 6, // Gross
                'cost_price' => 10000, 'selling_price' => 18000, 'stock' => 100
            ],
            [
                'sku' => 'JRM-001', 'name' => 'Jarum Mesin Jahit ORGAN DBx1 #11',
                'category_id' => 4, 'unit_id' => 8, // Pack
                'cost_price' => 18000, 'selling_price' => 25000, 'stock' => 50
            ],
            [
                'sku' => 'AKS-001', 'name' => 'Gunting Potong Kain KAI 10 inch',
                'category_id' => 5, 'unit_id' => 9, // Pcs
                'cost_price' => 180000, 'selling_price' => 225000, 'stock' => 15
            ],
            [
                'sku' => 'SPT-001', 'name' => 'Minyak Mesin Jahit Singer (100cc)',
                'category_id' => 6, 'unit_id' => 10, // Botol
                'cost_price' => 8000, 'selling_price' => 15000, 'stock' => 45
            ],
        ];

        foreach ($products as $prod) {
            Product::updateOrCreate(['sku' => $prod['sku']], $prod);
        }
    }
}
