<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Customer;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $customers = Customer::all();
        $products = Product::with('productUnits')->get();

        if ($users->isEmpty() || $products->isEmpty()) {
            return;
        }

        $startDate = Carbon::now()->subMonths(3);
        $endDate = Carbon::now();
        $numberOfOrders = rand(80, 100);

        for ($i = 0; $i < $numberOfOrders; $i++) {
            $user = $users->random();
            $customer = $customers->random();
            $date = Carbon::createFromTimestamp(rand($startDate->timestamp, $endDate->timestamp));
            
            // First we need to determine the items and subtotal so we can check credit limit
            $numItems = rand(1, 5);
            $subtotal = 0;
            $itemsData = [];

            for ($j = 0; $j < $numItems; $j++) {
                $product = $products->random();
                $productUnit = $product->productUnits->random();
                
                $qty = rand(1, 10);
                
                $price = $customer->type === 'wholesale' 
                    ? $productUnit->selling_price_wholesale 
                    : $productUnit->selling_price_retail;
                
                $itemSubtotal = $qty * $price;
                $subtotal += $itemSubtotal;
                
                $itemsData[] = [
                    'product' => $product,
                    'productUnit' => $productUnit,
                    'qty' => $qty,
                    'price' => $price,
                    'itemSubtotal' => $itemSubtotal,
                ];
            }

            $discount = rand(0, 1) ? rand(10000, 50000) : 0;
            // Prevent negative total
            if ($discount > $subtotal) $discount = 0;
            
            $total = $subtotal - $discount;

            $status = ['completed', 'completed', 'completed', 'piutang'][rand(0, 3)];
            $paymentMethod = ['cash', 'cash', 'transfer', 'qris'][rand(0, 3)];
            
            if ($customer->type === 'retail') {
                $status = 'completed'; // Retail usually pays in full
            }
            
            // Check Credit Limit Anomaly
            if ($status === 'piutang') {
                if ($customer->max_credit_limit > 0) {
                    $currentDebt = Order::where('customer_id', $customer->id)->where('status', 'piutang')->sum('total');
                    if (($currentDebt + $total) > $customer->max_credit_limit) {
                        $status = 'completed'; // Fallback to completed to prevent anomaly
                        $paymentMethod = 'transfer';
                    }
                } else {
                    $status = 'completed';
                }
            }

            $order = Order::create([
                'receipt_number' => 'INV-' . $date->format('Ymd') . '-' . strtoupper(Str::random(5)),
                'user_id' => $user->id,
                'customer_id' => $customer->id,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'total' => $total,
                'payment_method' => $paymentMethod,
                'status' => $status,
                'created_at' => $date,
                'updated_at' => $date,
            ]);

            foreach ($itemsData as $data) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $data['product']->id,
                    'qty' => $data['qty'],
                    'price' => $data['price'],
                    'subtotal' => $data['itemSubtotal'],
                    'created_at' => $date,
                    'updated_at' => $date,
                ]);

                // Adjust stock (using the conversion rate)
                $qtyInBaseUnit = $data['qty'] * $data['productUnit']->conversion_rate;
                
                // Get fresh product to ensure stock is accurate
                $freshProduct = Product::find($data['product']->id);
                $stockBefore = $freshProduct->stock;
                
                $freshProduct->decrement('stock', $qtyInBaseUnit);
                
                \App\Models\StockLedger::create([
                    'product_id' => $freshProduct->id,
                    'user_id' => $user->id,
                    'type' => 'out',
                    'qty' => -$qtyInBaseUnit, // out is negative
                    'stock_before' => $stockBefore,
                    'stock_after' => $stockBefore - $qtyInBaseUnit,
                    'reference_type' => Order::class,
                    'reference_id' => $order->id,
                    'note' => 'Penjualan ' . $order->receipt_number,
                    'created_at' => $date,
                    'updated_at' => $date,
                ]);
            }
        }
    }
}
