<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class SecurityConcurrencyTest extends TestCase
{
    use RefreshDatabase;

    public function test_master_data_uses_soft_deletes()
    {
        $product = Product::create([
            'sku' => 'TEST-01',
            'name' => 'Soft Delete Product',
            'cost_price' => 50000,
            'selling_price' => 70000,
            'stock' => 10,
        ]);

        $product->delete();

        $this->assertSoftDeleted($product);
    }
}
