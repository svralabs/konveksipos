<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Laba Rugi</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #1e293b; line-height: 1.4; }
        .header { text-align: center; margin-bottom: 16px; border-bottom: 2px solid #0f172a; padding-bottom: 8px; }
        .header h1 { margin: 0; font-size: 18px; text-transform: uppercase; letter-spacing: 0.5px; }
        .header p { margin: 3px 0 0 0; color: #64748b; font-size: 10px; }
        
        .alert-deficit { background-color: #fef2f2; border-left: 4px solid #ef4444; border-top: 1px solid #fecaca; border-bottom: 1px solid #fecaca; border-right: 1px solid #fecaca; padding: 8px 12px; margin-bottom: 16px; border-radius: 4px; }
        .alert-title { font-size: 11px; font-weight: bold; color: #991b1b; text-transform: uppercase; }
        .alert-desc { font-size: 9.5px; color: #b91c1c; margin-top: 2px; }

        .section { margin-bottom: 16px; }
        .section-title { font-size: 12px; font-weight: bold; border-bottom: 1px solid #cbd5e1; padding-bottom: 4px; margin-bottom: 8px; color: #0f172a; text-transform: uppercase; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 6px; margin-bottom: 8px; }
        th { background-color: #f1f5f9; color: #475569; font-size: 9.5px; font-weight: bold; text-transform: uppercase; padding: 6px 8px; text-align: left; border: 1px solid #e2e8f0; }
        td { padding: 5px 8px; border: 1px solid #e2e8f0; font-size: 10px; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        
        .highlight { background-color: #f8fafc; padding: 8px 12px; margin-top: 10px; margin-bottom: 14px; border: 1px solid #cbd5e1; border-radius: 6px; }
        .highlight-title { float: left; font-weight: bold; font-size: 12px; }
        .highlight-amount { float: right; font-weight: bold; font-size: 13px; }
        
        .net-highlight { background-color: #ecfdf5; border: 1.5px solid #a7f3d0; padding: 10px 14px; margin-top: 14px; border-radius: 8px; }
        .net-title { float: left; font-weight: bold; font-size: 14px; color: #065f46; }
        .net-amount { float: right; font-weight: bold; font-size: 15px; color: #047857; }
        
        .text-red { color: #dc2626; }
        .text-green { color: #059669; }
        .text-amber { color: #d97706; }
        .clearfix::after { content: ""; clear: both; display: table; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Laba Rugi (Profit & Loss)</h1>
        <p>Periode: {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') }} - {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') }}</p>
    </div>

    @if($isDeficit)
    <div class="alert-deficit">
        <div class="alert-title">⚠️ PERHATIAN: OPERASIONAL MEMILIKI DEFISIT / RUGI NETT (Rp {{ number_format($netProfit, 0, ',', '.') }})</div>
        <div class="alert-desc">Total HPP modal produk dan biaya operasional melebihi pendapatan penjualan. Margin Bersih: {{ number_format($netMargin, 1) }}%.</div>
    </div>
    @endif

    <!-- 1. PENDAPATAN -->
    <div class="section">
        <div class="section-title">1. PENDAPATAN PENJUALAN (TOTAL: Rp {{ number_format($revenue, 0, ',', '.') }})</div>
        <p style="font-size: 9.5px; color: #64748b; margin-bottom: 4px;">
            Total {{ $totalOrdersCount }} Transaksi ({{ $completedOrdersCount }} Lunas, {{ $piutangOrdersCount }} Tempo/Piutang)
        </p>

        <table>
            <thead>
                <tr>
                    <th>Metode Pembayaran</th>
                    <th class="text-center">Jml Transaksi</th>
                    <th class="text-right">Nominal Subtotal</th>
                    <th class="text-right">% Omset</th>
                </tr>
            </thead>
            <tbody>
                @foreach($revenueByPayment as $pay)
                <tr>
                    <td class="font-bold">{{ $pay['label'] }}</td>
                    <td class="text-center">{{ $pay['count'] }}</td>
                    <td class="text-right">Rp {{ number_format($pay['amount'], 0, ',', '.') }}</td>
                    <td class="text-right text-green font-bold">{{ $revenue > 0 ? number_format(($pay['amount'] / $revenue) * 100, 1) : 0 }}%</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- 2. HPP -->
    <div class="section">
        <div class="section-title">2. HARGA POKOK PENJUALAN / MODAL PRODUK (TOTAL: Rp {{ number_format($cogs, 0, ',', '.') }})</div>
        
        <table>
            <thead>
                <tr>
                    <th>Nama Produk</th>
                    <th class="text-center">Qty Terjual</th>
                    <th class="text-right">Modal Satuan</th>
                    <th class="text-right">Total Nominal HPP</th>
                    <th class="text-right">% Kontribusi HPP</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cogsProductBreakdown as $prod)
                <tr>
                    <td class="font-bold">{{ $prod['name'] }}</td>
                    <td class="text-center">{{ $prod['qty'] }} unit</td>
                    <td class="text-right">Rp {{ number_format($prod['cost_price'], 0, ',', '.') }}</td>
                    <td class="text-right text-red font-bold">Rp {{ number_format($prod['total_cogs'], 0, ',', '.') }}</td>
                    <td class="text-right text-red font-bold">{{ number_format($prod['percentage'], 1) }}%</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- LABA KOTOR HIGHLIGHT -->
    <div class="highlight clearfix">
        <div class="highlight-title">LABA KOTOR (GROSS PROFIT)</div>
        <div class="highlight-amount {{ $grossProfit < 0 ? 'text-red' : 'text-green' }}">
            Rp {{ number_format($grossProfit, 0, ',', '.') }} (Margin: {{ number_format($grossMargin, 1) }}%)
        </div>
    </div>

    <!-- 3. BIAYA OPERASIONAL -->
    <div class="section">
        <div class="section-title">3. BIAYA OPERASIONAL (TOTAL: Rp {{ number_format($expenses, 0, ',', '.') }})</div>
        
        <table>
            <thead>
                <tr>
                    <th>Kategori Pengeluaran</th>
                    <th class="text-center">Catatan</th>
                    <th class="text-right">Total Nominal Biaya</th>
                    <th class="text-right">% Beban</th>
                </tr>
            </thead>
            <tbody>
                @foreach($expensesCategoryBreakdown as $expCat)
                <tr>
                    <td class="font-bold">{{ $expCat['label'] }}</td>
                    <td class="text-center">{{ $expCat['count'] }}</td>
                    <td class="text-right text-amber font-bold">Rp {{ number_format($expCat['amount'], 0, ',', '.') }}</td>
                    <td class="text-right text-amber font-bold">{{ $expenses > 0 ? number_format(($expCat['amount'] / $expenses) * 100, 1) : 0 }}%</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- LABA BERSIH HIGHLIGHT -->
    <div class="net-highlight clearfix" style="{{ $isDeficit ? 'background-color: #fef2f2; border-color: #fca5a5;' : '' }}">
        <div class="net-title" style="{{ $isDeficit ? 'color: #991b1b;' : '' }}">LABA BERSIH (NET PROFIT)</div>
        <div class="net-amount {{ $netProfit < 0 ? 'text-red' : 'text-green' }}">
            Rp {{ number_format($netProfit, 0, ',', '.') }} (Margin: {{ number_format($netMargin, 1) }}%)
        </div>
    </div>
</body>
</html>
