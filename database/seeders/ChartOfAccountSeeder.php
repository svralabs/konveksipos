<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ChartOfAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $accounts = [
            ['account_code' => '1101', 'name' => 'Kas & Bank', 'type' => 'asset'],
            ['account_code' => '1102', 'name' => 'Piutang Usaha', 'type' => 'asset'],
            ['account_code' => '1103', 'name' => 'Persediaan Barang', 'type' => 'asset'],
            ['account_code' => '4101', 'name' => 'Pendapatan Penjualan', 'type' => 'revenue'],
            ['account_code' => '5101', 'name' => 'Harga Pokok Penjualan', 'type' => 'expense'],
            ['account_code' => '6101', 'name' => 'Biaya Operasional', 'type' => 'expense'],
        ];

        foreach ($accounts as $account) {
            \App\Models\ChartOfAccount::firstOrCreate(
                ['account_code' => $account['account_code']],
                $account
            );
        }
    }
}
