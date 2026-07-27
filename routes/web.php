<?php

use Illuminate\Support\Facades\Route;
use App\Models\Order;
use App\Services\ReceiptPrinter;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/receipt/{order}', function (Order $order) {
        $receipt = ReceiptPrinter::buildReceiptData($order->load('items.product', 'customer'));
        return view('receipt.print', compact('receipt'));
    })->name('receipt.print');

    Route::get('/invoice/{order}/pdf', function (Order $order) {
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('receipt.invoice-pdf', ['order' => $order->load('items.product', 'customer', 'user')]);
        return $pdf->download('Invoice-' . $order->receipt_number . '.pdf');
    })->name('invoice.pdf');
});
