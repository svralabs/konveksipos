<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\Unit;
use App\Models\StockLedger;
use App\Models\User;
use Carbon\Carbon;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $kainCat = Category::where('name', 'Kain')->first();
        $benangCat = Category::where('name', 'Benang')->first();
        $resletingCat = Category::where('name', 'Resleting')->first();
        $jarumCat = Category::where('name', 'Jarum')->first();
        $mesinCat = Category::where('name', 'Mesin Jahit')->first();

        $roll = Unit::where('code', 'roll')->first();
        $meter = Unit::where('code', 'mtr')->first();
        $lusin = Unit::where('code', 'lsn')->first();
        $pcs = Unit::where('code', 'pcs')->first();
        $gross = Unit::where('code', 'grs')->first();
        $box = Unit::where('code', 'box')->first();
        
        $adminGudang = User::whereHas('roles', function($q){
            $q->where('name', 'admin_gudang');
        })->first() ?? User::first();
        
        $startDate = Carbon::now()->subMonths(3)->subDay(); // initial stock before any transactions

        $products = [
            [
                'sku' => 'KN-CC-30S-BL',
                'name' => 'Kain Cotton Combed 30s Hitam',
                'category_id' => $kainCat->id,
                'unit_id' => $roll->id, // Base unit Roll
                'cost_price' => 1500000,
                'selling_price' => 1800000,
                'stock' => 10000,
                'min_stock' => 100,
                'units' => [
                    ['unit_id' => $roll->id, 'is_base' => true, 'conversion_rate' => 1, 'selling_price_retail' => 1800000, 'selling_price_wholesale' => 1700000],
                    ['unit_id' => $meter->id, 'is_base' => false, 'conversion_rate' => 50, 'selling_price_retail' => 40000, 'selling_price_wholesale' => 38000],
                ]
            ],
            [
                'sku' => 'BN-OB-AST-WT',
                'name' => 'Benang Obras Astra Putih',
                'category_id' => $benangCat->id,
                'unit_id' => $lusin->id,
                'cost_price' => 120000,
                'selling_price' => 150000,
                'stock' => 5000,
                'min_stock' => 200,
                'units' => [
                    ['unit_id' => $lusin->id, 'is_base' => true, 'conversion_rate' => 1, 'selling_price_retail' => 150000, 'selling_price_wholesale' => 135000],
                    ['unit_id' => $pcs->id, 'is_base' => false, 'conversion_rate' => 12, 'selling_price_retail' => 15000, 'selling_price_wholesale' => 13000],
                ]
            ],
            [
                'sku' => 'RS-YKK-JP-50CM',
                'name' => 'Resleting YKK Jepang 50cm',
                'category_id' => $resletingCat->id,
                'unit_id' => $gross->id,
                'cost_price' => 250000,
                'selling_price' => 300000,
                'stock' => 20000, // Very high because transaction decreases by 144 sometimes!
                'min_stock' => 500,
                'units' => [
                    ['unit_id' => $gross->id, 'is_base' => true, 'conversion_rate' => 1, 'selling_price_retail' => 300000, 'selling_price_wholesale' => 280000],
                    ['unit_id' => $lusin->id, 'is_base' => false, 'conversion_rate' => 12, 'selling_price_retail' => 30000, 'selling_price_wholesale' => 27000],
                    ['unit_id' => $pcs->id, 'is_base' => false, 'conversion_rate' => 144, 'selling_price_retail' => 3000, 'selling_price_wholesale' => 2500],
                ]
            ],
            [
                'sku' => 'JR-ORG-DBX1-11',
                'name' => 'Jarum Organ DBx1 No. 11',
                'category_id' => $jarumCat->id,
                'unit_id' => $box->id,
                'cost_price' => 45000,
                'selling_price' => 60000,
                'stock' => 5000,
                'min_stock' => 200,
                'units' => [
                    ['unit_id' => $box->id, 'is_base' => true, 'conversion_rate' => 1, 'selling_price_retail' => 60000, 'selling_price_wholesale' => 55000],
                    ['unit_id' => $pcs->id, 'is_base' => false, 'conversion_rate' => 10, 'selling_price_retail' => 7000, 'selling_price_wholesale' => 6500],
                ]
            ],
            [
                'sku' => 'MS-JUK-DDL8100',
                'name' => 'Mesin Jahit Juki DDL-8100e',
                'category_id' => $mesinCat->id,
                'unit_id' => $pcs->id,
                'cost_price' => 2800000,
                'selling_price' => 3200000,
                'stock' => 500,
                'min_stock' => 10,
                'units' => [
                    ['unit_id' => $pcs->id, 'is_base' => true, 'conversion_rate' => 1, 'selling_price_retail' => 3200000, 'selling_price_wholesale' => 3100000],
                ]
            ]
        ];

        foreach ($products as $pData) {
            $units = $pData['units'];
            unset($pData['units']);

            $product = Product::firstOrCreate(['sku' => $pData['sku']], $pData);
            
            // Add initial stock ledger entry to fix the first anomaly
            StockLedger::create([
                'product_id' => $product->id,
                'user_id' => $adminGudang->id ?? null,
                'type' => 'in',
                'qty' => $product->stock,
                'stock_before' => 0,
                'stock_after' => $product->stock,
                'reference_type' => 'App\Models\Product',
                'reference_id' => $product->id,
                'note' => 'Initial Stock (Opening Balance)',
                'created_at' => $startDate,
                'updated_at' => $startDate,
            ]);

            foreach ($units as $uData) {
                $product->productUnits()->updateOrCreate(
                    ['unit_id' => $uData['unit_id']],
                    $uData
                );
            }
        }
    }
}
