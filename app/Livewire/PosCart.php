<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use Filament\Notifications\Notification;

class PosCart extends Component
{
    public string $search = '';
    public $selectedCategory = null;

    public array $cart = [];
    public $customerId = null;
    public string $paymentMethod = 'cash';
    public $subtotal = 0;
    public $discount = 0;
    public $total = 0;
    
    public $tenderedAmount = null;
    public $changeAmount = 0;
    
    public $lastOrderId = null;

    // Credit limit check
    public $creditLimitExceeded = false;
    public $creditLimitInfo = '';

    // Shift management
    public bool $isShiftOpen = false;
    public bool $shiftModalOpen = false;
    public $openingAmount = 0;

    public bool $closeShiftModalOpen = false;
    public $closingAmount = 0;

    public function mount()
    {
        $this->checkShift();
    }

    public function checkShift()
    {
        $activeShift = \App\Models\CashRegister::where('user_id', auth()->id())
            ->where('status', 'open')
            ->first();

        if ($activeShift) {
            $this->isShiftOpen = true;
            $this->shiftModalOpen = false;
        } else {
            $this->isShiftOpen = false;
            $this->shiftModalOpen = true;
        }
    }

    public function openShift()
    {
        $this->validate([
            'openingAmount' => 'required|numeric|min:0'
        ]);

        \App\Models\CashRegister::create([
            'user_id' => auth()->id(),
            'opening_amount' => $this->openingAmount,
            'status' => 'open',
            'opened_at' => now(),
        ]);

        $this->isShiftOpen = true;
        $this->shiftModalOpen = false;

        Notification::make()->title('Shift Kasir Dibuka')->success()->send();
    }

    public function promptCloseShift()
    {
        $this->closeShiftModalOpen = true;
    }

    public function closeShift()
    {
        $this->validate([
            'closingAmount' => 'required|numeric|min:0'
        ]);

        $activeShift = \App\Models\CashRegister::where('user_id', auth()->id())
            ->where('status', 'open')
            ->first();

        if ($activeShift) {
            $activeShift->update([
                'closing_amount' => $this->closingAmount,
                'status' => 'closed',
                'closed_at' => now(),
            ]);
        }

        $this->isShiftOpen = false;
        $this->closeShiftModalOpen = false;
        $this->shiftModalOpen = true; 

        Notification::make()->title('Shift Kasir Ditutup (EOD)')->success()->send();
    }

    public function setCategory($id): void
    {
        $this->selectedCategory = $id;
    }

    public function scanProduct(): void
    {
        if (empty($this->search)) return;

        $product = Product::where('sku', $this->search)->first();

        if ($product) {
            $this->addProductById($product->id);
            $this->search = '';
        }
    }

    public function addProductById($id): void
    {
        $product = Product::find($id);
        if (!$product) return;

        // Get price based on customer type (Tier Pricing)
        $price = $this->getPriceForProduct($product);

        if (isset($this->cart[$product->id])) {
            $this->cart[$product->id]['qty']++;
            $this->cart[$product->id]['subtotal'] = $this->cart[$product->id]['qty'] * $this->cart[$product->id]['price'];
        } else {
            $this->cart[$product->id] = [
                'id'       => $product->id,
                'name'     => $product->name,
                'price'    => $price,
                'qty'      => 1,
                'subtotal' => $price,
            ];
        }

        $this->calculateTotal();
        $this->checkCreditLimit();
        Notification::make()->title($product->name . ' ditambahkan')->success()->send();
    }

    private function getPriceForProduct(Product $product): float
    {
        // Tier Pricing: if customer is wholesale, try to get wholesale price from product_units
        if ($this->customerId) {
            $customer = Customer::find($this->customerId);
            if ($customer && $customer->isWholesale()) {
                // Try to find wholesale price in product_units (base unit)
                $productUnit = $product->productUnits()
                    ->where('is_base', true)
                    ->first();
                if ($productUnit && $productUnit->selling_price_wholesale > 0) {
                    return (float) $productUnit->selling_price_wholesale;
                }
            }
        }
        // Default: use selling_price
        return (float) $product->selling_price;
    }

    public function updatedCustomerId(): void
    {
        // Re-price all items when customer changes
        foreach ($this->cart as $id => $item) {
            $product = Product::find($id);
            if ($product) {
                $newPrice = $this->getPriceForProduct($product);
                $this->cart[$id]['price'] = $newPrice;
                $this->cart[$id]['subtotal'] = $this->cart[$id]['qty'] * $newPrice;
            }
        }
        $this->calculateTotal();
        $this->checkCreditLimit();
    }

    public function updatedPaymentMethod(): void
    {
        $this->checkCreditLimit();
    }

    public function updatedDiscount(): void
    {
        $this->discount = max(0, (int) $this->discount);
        $this->calculateTotal();
        $this->checkCreditLimit();
    }

    public function updatedTenderedAmount(): void
    {
        $this->calculateChange();
    }

    private function calculateChange(): void
    {
        if ($this->paymentMethod === 'cash' && $this->tenderedAmount !== null) {
            $this->changeAmount = max(0, (int) $this->tenderedAmount - $this->total);
        } else {
            $this->changeAmount = 0;
        }
    }

    private function checkCreditLimit(): void
    {
        $this->creditLimitExceeded = false;
        $this->creditLimitInfo = '';

        if ($this->paymentMethod !== 'piutang' || !$this->customerId) return;

        $customer = Customer::find($this->customerId);
        if (!$customer || $customer->max_credit_limit <= 0) return;

        $outstanding = $customer->getTotalOutstandingDebt();
        $remaining   = $customer->max_credit_limit - $outstanding;

        if (($outstanding + $this->total) > $customer->max_credit_limit) {
            $this->creditLimitExceeded = true;
            $this->creditLimitInfo = sprintf(
                'Limit kredit Rp %s. Piutang berjalan Rp %s. Sisa limit Rp %s.',
                number_format($customer->max_credit_limit, 0, ',', '.'),
                number_format($outstanding, 0, ',', '.'),
                number_format(max(0, $remaining), 0, ',', '.')
            );
        }
    }

    public function incrementQty($id): void
    {
        if (isset($this->cart[$id])) {
            $this->cart[$id]['qty']++;
            $this->cart[$id]['subtotal'] = $this->cart[$id]['qty'] * $this->cart[$id]['price'];
            $this->calculateTotal();
            $this->checkCreditLimit();
        }
    }

    public function decrementQty($id): void
    {
        if (isset($this->cart[$id])) {
            if ($this->cart[$id]['qty'] > 1) {
                $this->cart[$id]['qty']--;
                $this->cart[$id]['subtotal'] = $this->cart[$id]['qty'] * $this->cart[$id]['price'];
            } else {
                unset($this->cart[$id]);
            }
            $this->calculateTotal();
            $this->checkCreditLimit();
        }
    }

    public function removeItem($id): void
    {
        unset($this->cart[$id]);
        $this->calculateTotal();
        $this->checkCreditLimit();
    }

    public function calculateTotal(): void
    {
        $this->subtotal = array_sum(array_column($this->cart, 'subtotal'));
        // Prevent total from going negative if discount is too large
        $this->total = max(0, $this->subtotal - (int)$this->discount);
        $this->calculateChange();
    }

    public function checkout()
    {
        if (!$this->isShiftOpen) {
            Notification::make()->title('Gagal')->body('Silakan buka shift kasir terlebih dahulu.')->danger()->send();
            return;
        }
        
        if (empty($this->cart)) {
            Notification::make()->title('Gagal')->body('Keranjang kosong.')->danger()->send();
            return;
        }

        if ($this->creditLimitExceeded) {
            Notification::make()->title('Limit kredit terlampaui!')->body($this->creditLimitInfo)->danger()->send();
            return;
        }

        // Validation for Cash Payment
        if ($this->paymentMethod === 'cash') {
            $tendered = (int) $this->tenderedAmount;
            if ($tendered < $this->total) {
                Notification::make()->title('Uang dibayar kurang!')->body('Uang yang dibayar tidak boleh kurang dari Total Bayar.')->danger()->send();
                return;
            }
        }

        $createdOrderId = null;
        DB::transaction(function () use (&$createdOrderId) {
            $status = $this->paymentMethod === 'piutang' ? 'piutang' : 'completed';
            
            // Protect against negative subtotal edge cases
            $finalSubtotal = max(0, $this->subtotal);
            $finalDiscount = min((int)$this->discount, $finalSubtotal); // discount max is subtotal
            $finalTotal = $finalSubtotal - $finalDiscount;

            $order = Order::create([
                'receipt_number' => 'INV-' . date('Ymd') . '-' . str_pad(Order::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT),
                'user_id'        => auth()->id(),
                'customer_id'    => $this->customerId ?: null,
                'subtotal'       => $finalSubtotal,
                'discount'       => $finalDiscount,
                'total'          => $finalTotal,
                'payment_method' => $this->paymentMethod,
                'status'         => $status,
                'tendered_amount'=> $this->tenderedAmount ?? $this->total,
                'change_amount'  => $this->changeAmount,
            ]);
            
            $createdOrderId = $order->id;

            foreach ($this->cart as $item) {
                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $item['id'],
                    'qty'        => $item['qty'],
                    'price'      => $item['price'],
                    'subtotal'   => $item['subtotal'],
                ]);

                // Decrement stock + log to ledger
                $product = Product::where('id', $item['id'])->lockForUpdate()->first();
                $product?->adjustStock(
                    qty: -$item['qty'],
                    type: 'out',
                    note: 'Penjualan #' . $order->receipt_number,
                    userId: auth()->id(),
                    referenceType: Order::class,
                    referenceId: $order->id,
                );
            }

            // ── Ledger: Auto-journal via laravel-wallet ──
            $cashier = auth()->user();
            $amountInt = (int) round($order->total * 100); // wallet uses integer (cents)

            if ($order->payment_method === 'piutang' && $order->customer_id) {
                // Receivable: deposit to customer's wallet as debt tracking
                $customer = \App\Models\Customer::find($order->customer_id);
                if ($customer) {
                    $customer->deposit($amountInt, [
                        'description' => 'Piutang dari transaksi #' . $order->receipt_number,
                        'order_id'    => $order->id,
                    ]);
                }
            } else {
                // Cash/Transfer: deposit to cashier's wallet as revenue
                $cashier->deposit($amountInt, [
                    'description' => 'Penjualan #' . $order->receipt_number,
                    'order_id'    => $order->id,
                ]);
            }

            // ── Enterprise General Ledger (Double-Entry Bookkeeping) ──
            $cogsTotal = 0;
            foreach ($this->cart as $item) {
                $p = Product::find($item['id']);
                if ($p) {
                    $cogsTotal += ($p->cost_price * $item['qty']);
                }
            }
            
            $debitAccountCode = $order->payment_method === 'piutang' ? '1102' : '1101';

            // 1. Jurnal Penjualan
            \App\Models\GeneralLedger::create([
                'transaction_date' => now()->toDateString(),
                'chart_of_account_id' => \App\Models\ChartOfAccount::where('account_code', $debitAccountCode)->first()->id,
                'reference_type' => Order::class,
                'reference_id' => $order->id,
                'description' => 'Penjualan #' . $order->receipt_number,
                'debit' => $order->total,
                'credit' => 0,
            ]);

            \App\Models\GeneralLedger::create([
                'transaction_date' => now()->toDateString(),
                'chart_of_account_id' => \App\Models\ChartOfAccount::where('account_code', '4101')->first()->id, // Pendapatan
                'reference_type' => Order::class,
                'reference_id' => $order->id,
                'description' => 'Pendapatan Penjualan #' . $order->receipt_number,
                'debit' => 0,
                'credit' => $order->total,
            ]);

            // 2. Jurnal HPP
            if ($cogsTotal > 0) {
                \App\Models\GeneralLedger::create([
                    'transaction_date' => now()->toDateString(),
                    'chart_of_account_id' => \App\Models\ChartOfAccount::where('account_code', '5101')->first()->id, // HPP
                    'reference_type' => Order::class,
                    'reference_id' => $order->id,
                    'description' => 'HPP Penjualan #' . $order->receipt_number,
                    'debit' => $cogsTotal,
                    'credit' => 0,
                ]);

                \App\Models\GeneralLedger::create([
                    'transaction_date' => now()->toDateString(),
                    'chart_of_account_id' => \App\Models\ChartOfAccount::where('account_code', '1103')->first()->id, // Persediaan
                    'reference_type' => Order::class,
                    'reference_id' => $order->id,
                    'description' => 'Persediaan Keluar #' . $order->receipt_number,
                    'debit' => 0,
                    'credit' => $cogsTotal,
                ]);
            }
        });

        $this->cart = [];
        $this->customerId = null;
        $this->paymentMethod = 'cash';
        $this->discount = 0;
        $this->tenderedAmount = null;
        $this->changeAmount = 0;
        $this->creditLimitExceeded = false;
        $this->creditLimitInfo = '';
        $this->calculateTotal();
        $this->lastOrderId = $createdOrderId;

        Notification::make()->title('Transaksi Berhasil! 🎉')->success()->send();
    }

    public function render()
    {
        $categories = Category::orderBy('name', 'asc')->get();

        $productsQuery = Product::query();

        if ($this->selectedCategory) {
            $productsQuery->where('category_id', $this->selectedCategory);
        }

        if ($this->search) {
            $productsQuery->where(function ($q) {
                $q->where('name', 'ilike', '%' . $this->search . '%')
                  ->orWhere('sku', 'ilike', '%' . $this->search . '%');
            });
        }

        $products  = $productsQuery->orderBy('name')->get();
        $customers = Customer::orderBy('name')->get();

        return view('livewire.pos-cart', [
            'categories' => $categories,
            'products'   => $products,
            'customers'  => $customers,
        ]);
    }
}
