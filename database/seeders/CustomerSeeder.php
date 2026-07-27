<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $customers = [
            [
                'name' => 'Konveksi Maju Jaya', 
                'type' => 'wholesale', 
                'phone' => '085600010001', 
                'email' => 'majujaya@example.com',
                'address' => 'Jl. Kebon Jeruk No. 1, Jakarta',
                'max_credit_limit' => 50000000
            ],
            [
                'name' => 'Penjahit Pak Budi', 
                'type' => 'retail', 
                'phone' => '085600010002', 
                'email' => 'budi.tailor@example.com',
                'address' => 'Pasar Tanah Abang Blok A',
                'max_credit_limit' => 5000000
            ],
            [
                'name' => 'Garment Nusantara', 
                'type' => 'wholesale', 
                'phone' => '085600010003', 
                'email' => 'nusantara.garment@example.com',
                'address' => 'Kawasan Industri Pulogadung',
                'max_credit_limit' => 100000000
            ],
            [
                'name' => 'Konveksi Kaos Polos', 
                'type' => 'wholesale', 
                'phone' => '085600010004', 
                'email' => 'kaospolos@example.com',
                'address' => 'Cimahi, Bandung',
                'max_credit_limit' => 20000000
            ],
            [
                'name' => 'Umum (Walk-in)', 
                'type' => 'retail', 
                'phone' => '', 
                'email' => '',
                'address' => '',
                'max_credit_limit' => 0
            ],
        ];

        foreach ($customers as $customer) {
            Customer::firstOrCreate(['name' => $customer['name']], $customer);
        }
    }
}
