<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PosTransactionTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_reduces_stock_and_logs_ledger()
    {
        // 1. Arrange
        $user = User::factory()->create();
        $this->actingAs($user);

        $product = Product::create([
            'sku' => 'TEST-01',
            'name' => 'Produk Test',
            'selling_price' => 10000,
            'cost_price' => 8000,
            'stock' => 50,
        ]);

        // 2. Act
        $product->adjustStock(-5, 'out', 'Penjualan Test POS', $user->id);

        // 3. Assert
        $this->assertEquals(45, $product->fresh()->stock);
        $this->assertDatabaseHas('stock_ledgers', [
            'product_id' => $product->id,
            'qty' => -5,
            'stock_before' => 50,
            'stock_after' => 45,
            'type' => 'out',
        ]);
    }

    public function test_credit_limit_blocking_logic()
    {
        // 1. Arrange
        $customer = Customer::create([
            'name' => 'Pelanggan Pabrik B2B',
            'type' => 'wholesale',
            'max_credit_limit' => 500000, // Rp 500.000 limit
        ]);

        $user = User::factory()->create();
        $this->actingAs($user);

        // 2. Act & Assert: Initially outstanding debt is 0
        $this->assertFalse($customer->isOverCreditLimit(100000)); // 100k doesn't exceed 500k limit

        // Create an unpaid debt order of 400.000
        Order::create([
            'receipt_number' => 'INV-TEST-001',
            'user_id' => $user->id,
            'customer_id' => $customer->id,
            'subtotal' => 400000,
            'total' => 400000,
            'payment_method' => 'piutang',
            'status' => 'piutang',
        ]);

        // 3. Assert credit limit checks
        $this->assertFalse($customer->isOverCreditLimit(50000));  // 400k + 50k = 450k (still below 500k)
        $this->assertTrue($customer->isOverCreditLimit(150000)); // 400k + 150k = 550k (exceeds 500k limit!)
    }
}
