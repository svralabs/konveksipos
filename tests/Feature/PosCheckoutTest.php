<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Livewire\Livewire;
use App\Livewire\PosCart;
use App\Models\Product;
use App\Models\Customer;
use App\Models\User;
use App\Models\Order;
use App\Models\ChartOfAccount;

class PosCheckoutTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Setup initial accounts for journaling
        ChartOfAccount::create(['account_code' => '1101', 'name' => 'Kas & Bank', 'type' => 'asset']);
        ChartOfAccount::create(['account_code' => '1102', 'name' => 'Piutang Usaha', 'type' => 'asset']);
        ChartOfAccount::create(['account_code' => '1103', 'name' => 'Persediaan', 'type' => 'asset']);
        ChartOfAccount::create(['account_code' => '4101', 'name' => 'Pendapatan Penjualan', 'type' => 'revenue']);
        ChartOfAccount::create(['account_code' => '5101', 'name' => 'HPP', 'type' => 'expense']);
        ChartOfAccount::create(['account_code' => '6101', 'name' => 'Biaya Operasional', 'type' => 'expense']);
    }

    public function test_pos_checkout_retail_customer()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create();
        $this->actingAs($user);
        
        $product = Product::create([
            'sku' => 'TEST-01',
            'name' => 'Test Retail Product',
            'cost_price' => 50000,
            'selling_price' => 70000,
            'stock' => 10,
        ]);

        $component = Livewire::test(PosCart::class)
            ->set('isShiftOpen', true)
            ->call('addProductById', $product->id)
            ->set('tenderedAmount', 100000)
            ->call('checkout');

        if ($component->errors()->isNotEmpty()) {
            dd($component->errors());
        }

        // Assert order created
        $this->assertDatabaseHas('orders', [
            'subtotal' => 70000,
            'total' => 70000,
            'payment_method' => 'cash',
        ]);

        // Assert stock reduced
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 9,
        ]);

        // Assert general ledger created (Debit Kas, Kredit Pendapatan)
        $this->assertDatabaseHas('general_ledgers', [
            'chart_of_account_id' => ChartOfAccount::where('account_code', '1101')->first()->id,
            'debit' => 70000,
        ]);
        
        $this->assertDatabaseHas('general_ledgers', [
            'chart_of_account_id' => ChartOfAccount::where('account_code', '4101')->first()->id,
            'credit' => 70000,
        ]);

        // Assert COGS ledger (Debit HPP, Kredit Persediaan)
        $this->assertDatabaseHas('general_ledgers', [
            'chart_of_account_id' => ChartOfAccount::where('account_code', '5101')->first()->id,
            'debit' => 50000,
        ]);
        
        $this->assertDatabaseHas('general_ledgers', [
            'chart_of_account_id' => ChartOfAccount::where('account_code', '1103')->first()->id,
            'credit' => 50000,
        ]);
    }

    public function test_pos_checkout_b2b_wholesale_pricing()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create();
        $this->actingAs($user);

        $customer = Customer::create([
            'name' => 'B2B Customer',
            'type' => 'wholesale',
            'max_credit_limit' => 1000000,
            'phone' => '08123456789'
        ]);
        
        $product = Product::create([
            'sku' => 'TEST-02',
            'name' => 'Test B2B Product',
            'cost_price' => 50000,
            'selling_price' => 70000,
            'stock' => 10,
        ]);

        $unit = \App\Models\Unit::create(['name' => 'Pcs', 'code' => 'PCS']);
        \App\Models\ProductUnit::create([
            'product_id' => $product->id,
            'unit_id' => $unit->id,
            'conversion_rate' => 1,
            'is_base' => true,
            'selling_price_wholesale' => 60000,
        ]);

        Livewire::test(PosCart::class)
            ->set('isShiftOpen', true)
            ->set('customerId', $customer->id)
            ->call('addProductById', $product->id)
            ->set('paymentMethod', 'piutang')
            ->call('checkout');

        // Order created with wholesale price
        $this->assertDatabaseHas('orders', [
            'subtotal' => 60000,
            'total' => 60000,
            'payment_method' => 'piutang',
        ]);
    }

    public function test_pos_checkout_exceeds_credit_limit()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $customer = Customer::create([
            'name' => 'B2B Poor Credit',
            'type' => 'wholesale',
            'max_credit_limit' => 50000, // Very low limit
            'phone' => '08123456788'
        ]);
        
        $product = Product::create([
            'sku' => 'TEST-03',
            'name' => 'Test Product',
            'cost_price' => 50000,
            'selling_price' => 70000,
            'stock' => 10,
        ]);

        Livewire::test(PosCart::class)
            ->set('isShiftOpen', true)
            ->set('customerId', $customer->id)
            ->call('addProductById', $product->id) // price is 70k > 50k limit
            ->set('paymentMethod', 'piutang')
            ->call('checkout');

        // Order should NOT be created
        $this->assertDatabaseMissing('orders', [
            'customer_id' => $customer->id,
        ]);
    }
}
