<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Kain',
            'Benang',
            'Resleting',
            'Jarum',
            'Mesin Jahit',
            'Aksesoris Jahit',
            'Kancing'
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(['name' => $category]);
        }
    }
}
