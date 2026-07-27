<?php
echo "=== Checking Orders ===\n";
$orders = App\Models\Order::all();
foreach ($orders as $o) {
    if ($o->subtotal - $o->discount != $o->total) {
        echo "Anomaly Order {$o->id}: Subtotal ({$o->subtotal}) - Discount ({$o->discount}) != Total ({$o->total})\n";
    }
}

echo "=== Checking Order Items ===\n";
$items = App\Models\OrderItem::all();
foreach ($items as $item) {
    if ($item->qty * $item->price != $item->subtotal) {
        echo "Anomaly OrderItem {$item->id}: Qty ({$item->qty}) * Price ({$item->price}) != Subtotal ({$item->subtotal})\n";
    }
}

echo "=== Checking Stock Ledgers ===\n";
$ledgers = App\Models\StockLedger::all();
foreach ($ledgers as $l) {
    if ($l->stock_before + $l->qty != $l->stock_after) {
        echo "Anomaly StockLedger {$l->id}: Before ({$l->stock_before}) + Qty ({$l->qty}) != After ({$l->stock_after})\n";
    }
}

echo "=== Checking Product Stocks ===\n";
$products = App\Models\Product::all();
foreach ($products as $p) {
    $calculatedStock = App\Models\StockLedger::where('product_id', $p->id)->sum('qty');
    // Product original stock in seeder is different? We added initial stock in ProductSeeder, 
    // but we didn't add it to StockLedger! This means calculatedStock won't match $p->stock.
    // Let's print out the difference to see.
    echo "Product {$p->id} '{$p->name}' - Current Stock: {$p->stock}, StockLedger Sum: {$calculatedStock}\n";
}

echo "=== Checking B2B Customers Debt ===\n";
$customers = App\Models\Customer::where('type', 'wholesale')->get();
foreach ($customers as $c) {
    $debt = App\Models\Order::where('customer_id', $c->id)->where('status', 'piutang')->sum('total');
    echo "Customer {$c->id} '{$c->name}' - Piutang: {$debt}, Max Credit: {$c->max_credit_limit}\n";
    if ($debt > $c->max_credit_limit && $c->max_credit_limit > 0) {
         echo "  => ANOMALY: Piutang exceeds max credit limit!\n";
    }
}

