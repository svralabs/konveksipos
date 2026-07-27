<?php

namespace App\Services;

use App\Models\Order;

/**
 * ReceiptPrinter
 *
 * Wrapper service untuk mencetak struk menggunakan mike42/escpos-php.
 * Untuk koneksi fisik, developer perlu konfigurasi printer di .env:
 *   PRINTER_TYPE=network|usb|file
 *   PRINTER_HOST=192.168.1.100  (untuk network printer)
 *   PRINTER_PORT=9100
 */
class ReceiptPrinter
{
    /**
     * Cetak struk via ESC/POS ke thermal printer.
     * Fallback ke HTML print jika printer tidak terkonfigurasi.
     */
    public static function printOrder(Order $order): bool
    {
        $printerType = config('printing.type', 'none');

        if ($printerType === 'network') {
            return self::printViaNetwork($order);
        }

        // Untuk development / fallback: tidak mencetak langsung
        // UI akan menampilkan tombol print-to-browser
        return false;
    }

    private static function printViaNetwork(Order $order): bool
    {
        try {
            $connector = new \Mike42\Escpos\PrintConnectors\NetworkPrintConnector(
                config('printing.host', '127.0.0.1'),
                config('printing.port', 9100)
            );

            $printer = new \Mike42\Escpos\Printer($connector);

            // Header
            $printer->setJustification(\Mike42\Escpos\Printer::JUSTIFY_CENTER);
            $printer->setTextSize(2, 2);
            $printer->text("KONVEKSI POS\n");
            $printer->setTextSize(1, 1);
            $printer->text("================================\n");

            // Info transaksi
            $printer->setJustification(\Mike42\Escpos\Printer::JUSTIFY_LEFT);
            $printer->text("No. : " . $order->receipt_number . "\n");
            $printer->text("Tgl : " . $order->created_at->format('d/m/Y H:i') . "\n");
            if ($order->customer) {
                $printer->text("Cust: " . $order->customer->name . "\n");
            }
            $printer->text("--------------------------------\n");

            // Items
            foreach ($order->items as $item) {
                $name  = substr($item->product?->name ?? '-', 0, 22);
                $qty   = $item->qty;
                $price = number_format($item->price, 0, ',', '.');
                $sub   = number_format($item->subtotal, 0, ',', '.');
                $printer->text(sprintf("%-22s\n  %s x Rp%-10s Rp%s\n", $name, $qty, $price, $sub));
            }

            $printer->text("================================\n");
            $printer->setJustification(\Mike42\Escpos\Printer::JUSTIFY_RIGHT);
            $printer->setEmphasis(true);
            $printer->text("TOTAL: Rp " . number_format($order->total, 0, ',', '.') . "\n");
            $printer->setEmphasis(false);
            $printer->text("Bayar: " . strtoupper($order->payment_method) . "\n");
            $printer->text("================================\n");

            $printer->setJustification(\Mike42\Escpos\Printer::JUSTIFY_CENTER);
            $printer->text("\nTerima Kasih!\n\n\n");
            $printer->cut();
            $printer->close();

            return true;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('ReceiptPrinter error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Generate struk dalam format array (untuk dirender di view HTML print).
     */
    public static function buildReceiptData(Order $order): array
    {
        return [
            'receipt_number' => $order->receipt_number,
            'date'           => $order->created_at->format('d/m/Y H:i'),
            'customer'       => $order->customer?->name ?? 'Pelanggan Umum',
            'cashier'        => $order->user?->name ?? '-',
            'items'          => $order->items->map(fn ($i) => [
                'name'     => $i->product?->name ?? '-',
                'qty'      => $i->qty,
                'price'    => $i->price,
                'subtotal' => $i->subtotal,
            ])->all(),
            'subtotal'       => $order->subtotal,
            'discount'       => $order->discount,
            'total'          => $order->total,
            'tendered'       => (float) ($order->tendered_amount ?? $order->total),
            'change'         => (float) ($order->change_amount ?? 0),
            'payment_method' => $order->payment_method,
            'status'         => $order->status,
        ];
    }
}
