<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice #{{ $order->receipt_number }}</title>
    <style>
        body { font-family: sans-serif; font-size: 13px; color: #333; line-height: 1.4; }
        .invoice-title { font-size: 22px; font-weight: bold; text-transform: uppercase; color: #1e3a8a; }
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .header-table td { vertical-align: top; }
        .info-block { margin-bottom: 20px; }
        .info-title { font-weight: bold; font-size: 11px; text-transform: uppercase; color: #666; margin-bottom: 4px; }
        .table { width: 100%; border-collapse: collapse; margin-top: 10px; margin-bottom: 30px; }
        .table th { background-color: #f3f4f6; font-weight: bold; text-align: left; padding: 10px; border-bottom: 2px solid #e5e7eb; }
        .table td { padding: 10px; border-bottom: 1px solid #e5e7eb; }
        .text-right { text-align: right; }
        .total-section { width: 40%; margin-left: 60%; margin-top: 20px; border-collapse: collapse; }
        .total-section td { padding: 8px 10px; }
        .total-section .grand-total { font-weight: bold; font-size: 16px; border-top: 2px solid #e5e7eb; color: #1e3a8a; }
        .footer-sig { width: 100%; margin-top: 50px; }
        .footer-sig td { text-align: center; width: 50%; }
        .sig-box { height: 70px; }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td>
                <div style="font-size: 18px; font-weight: bold; color: #1e3a8a;">KONVEKSI POS</div>
                <div style="color: #6b7280; font-size: 12px; margin-top: 3px;">Sistem POS & Keuangan Konveksi Terintegrasi</div>
                <div style="color: #6b7280; font-size: 11px;">Telp: (021) 12345678 | Email: info@konveksipos.test</div>
            </td>
            <td class="text-right">
                <div class="invoice-title">Invoice Tagihan</div>
                <div style="margin-top: 5px;">Nomor: <strong>{{ $order->receipt_number }}</strong></div>
                <div>Tanggal: {{ $order->created_at->format('d M Y H:i') }}</div>
            </td>
        </tr>
    </table>

    <table class="header-table">
        <tr>
            <td style="width: 50%;">
                <div class="info-block">
                    <div class="info-title">Penerima (Pelanggan B2B):</div>
                    <div style="font-size: 14px; font-weight: bold;">{{ $order->customer?->name ?? 'Pelanggan Umum' }}</div>
                    @if($order->customer?->phone) <div>Telp: {{ $order->customer->phone }}</div> @endif
                    @if($order->customer?->email) <div>Email: {{ $order->customer->email }}</div> @endif
                    @if($order->customer?->address) <div style="margin-top: 4px; color: #4b5563;">{{ $order->customer->address }}</div> @endif
                </div>
            </td>
            <td style="width: 50%;">
                <div class="info-block">
                    <div class="info-title">Metode & Status Pembayaran:</div>
                    <div>Metode Bayar: <strong>
                        @if($order->payment_method === 'cash') Tunai (Cash)
                        @elseif($order->payment_method === 'transfer') Transfer Bank
                        @else Piutang / Tempo
                        @endif
                    </strong></div>
                    <div>Status Transaksi: <span style="font-weight: bold; color: {{ $order->status === 'completed' ? '#16a34a' : '#dc2626' }};">
                        {{ $order->status === 'completed' ? 'LUNAS' : 'TEMPO / PIUTANG' }}
                    </span></div>
                </div>
            </td>
        </tr>
    </table>

    <table class="table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 50%;">Deskripsi Produk</th>
                <th style="width: 15%;" class="text-right">Harga</th>
                <th style="width: 10%;" class="text-right">Qty</th>
                <th style="width: 20%;" class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach($order->items as $item)
            <tr>
                <td>{{ $no++ }}</td>
                <td>
                    <div style="font-weight: bold;">{{ $item->product?->name }}</div>
                    <div style="font-size: 11px; color: #6b7280;">SKU: {{ $item->product?->sku }}</div>
                </td>
                <td class="text-right">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                <td class="text-right">{{ $item->qty }}</td>
                <td class="text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table class="total-section">
        <tr>
            <td>Subtotal</td>
            <td class="text-right">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</td>
        </tr>
        @if($order->discount > 0)
        <tr>
            <td>Diskon</td>
            <td class="text-right">-Rp {{ number_format($order->discount, 0, ',', '.') }}</td>
        </tr>
        @endif
        <tr class="grand-total">
            <td>TOTAL</td>
            <td class="text-right">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
        </tr>
    </table>

    <table class="footer-sig">
        <tr>
            <td>
                <div>Penerima,</div>
                <div class="sig-box"></div>
                <div style="text-decoration: underline; font-weight: bold;">{{ $order->customer?->name ?? '.......................' }}</div>
            </td>
            <td>
                <div>Hormat Kami,</div>
                <div class="sig-box"></div>
                <div style="text-decoration: underline; font-weight: bold;">{{ auth()->user()?->name ?? 'Kasir Konveksi' }}</div>
            </td>
        </tr>
    </table>
</body>
</html>
