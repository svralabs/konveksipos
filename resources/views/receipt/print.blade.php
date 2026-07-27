<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk #{{ $receipt['receipt_number'] }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Courier New', monospace; font-size: 12px; width: 300px; margin: 0 auto; padding: 8px; }
        .center { text-align: center; }
        .right { text-align: right; }
        .bold { font-weight: bold; }
        .separator { border-top: 1px dashed #000; margin: 6px 0; }
        .logo { font-size: 16px; font-weight: bold; text-align: center; margin-bottom: 4px; }
        .item-name { font-weight: bold; }
        .item-detail { padding-left: 8px; color: #333; }
        .total-line { display: flex; justify-content: space-between; margin-top: 4px; }
        .total-line.grand { font-weight: bold; font-size: 14px; border-top: 2px solid #000; padding-top: 4px; }
        .footer { text-align: center; margin-top: 12px; font-size: 11px; }
        @media print {
            body { margin: 0; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
    <div class="logo">★ KONVEKSI POS ★</div>
    <div class="center" style="font-size: 10px;">Toko Alat Konveksi</div>
    <div class="separator"></div>

    <div>No. Struk : <strong>{{ $receipt['receipt_number'] }}</strong></div>
    <div>Tanggal   : {{ $receipt['date'] }}</div>
    <div>Pelanggan : {{ $receipt['customer'] }}</div>
    <div>Kasir     : {{ $receipt['cashier'] }}</div>

    <div class="separator"></div>

    @foreach($receipt['items'] as $item)
    <div class="item-name">{{ $item['name'] }}</div>
    <div class="item-detail">
        {{ $item['qty'] }} x Rp {{ number_format($item['price'], 0, ',', '.') }}
        <span style="float: right">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</span>
    </div>
    @endforeach

    <div class="separator"></div>

    @if(isset($receipt['discount']) && $receipt['discount'] > 0)
    <div class="total-line">
        <span>Subtotal</span>
        <span>Rp {{ number_format($receipt['subtotal'], 0, ',', '.') }}</span>
    </div>
    <div class="total-line" style="color: #d97706;">
        <span>Diskon</span>
        <span>-Rp {{ number_format($receipt['discount'], 0, ',', '.') }}</span>
    </div>
    @endif

    <div class="total-line grand">
        <span>TOTAL</span>
        <span>Rp {{ number_format($receipt['total'], 0, ',', '.') }}</span>
    </div>
    
    @if($receipt['payment_method'] === 'cash' || $receipt['tendered'] > $receipt['total'])
    <div class="total-line">
        <span>Uang Bayar</span>
        <span>Rp {{ number_format($receipt['tendered'], 0, ',', '.') }}</span>
    </div>
    <div class="total-line" style="font-weight: bold; color: #166534;">
        <span>Kembalian</span>
        <span>Rp {{ number_format($receipt['change'], 0, ',', '.') }}</span>
    </div>
    @endif
    
    <div class="total-line">
        <span>Bayar (Via)</span>
        <span>{{ strtoupper($receipt['payment_method']) }}</span>
    </div>
    <div class="total-line">
        <span>Status</span>
        <span>{{ strtoupper($receipt['status']) }}</span>
    </div>

    <div class="footer">
        <div class="separator"></div>
        <p>Terima kasih atas kunjungan Anda!</p>
        <p>Barang yang sudah dibeli tidak dapat dikembalikan.</p>
    </div>

    @php
        $rawText = "KONVEKSI POS\n================================\n";
        $rawText .= "No. : " . $receipt['receipt_number'] . "\n";
        $rawText .= "Tgl : " . $receipt['date'] . "\n";
        $rawText .= "Cust: " . $receipt['customer'] . "\n";
        $rawText .= "Ksr : " . $receipt['cashier'] . "\n--------------------------------\n";
        foreach($receipt['items'] as $item) {
            $rawText .= substr(str_pad($item['name'], 22), 0, 22) . "\n";
            $rawText .= "  " . $item['qty'] . " x " . number_format($item['price'], 0, ',', '.') . "  " . number_format($item['subtotal'], 0, ',', '.') . "\n";
        }
        $rawText .= "================================\n";
        
        if (isset($receipt['discount']) && $receipt['discount'] > 0) {
            $rawText .= "Subtotal: Rp " . number_format($receipt['subtotal'], 0, ',', '.') . "\n";
            $rawText .= "Diskon  : -Rp " . number_format($receipt['discount'], 0, ',', '.') . "\n";
        }

        $rawText .= "TOTAL   : Rp " . number_format($receipt['total'], 0, ',', '.') . "\n";
        
        if ($receipt['payment_method'] === 'cash' || $receipt['tendered'] > $receipt['total']) {
            $rawText .= "Bayar   : Rp " . number_format($receipt['tendered'], 0, ',', '.') . "\n";
            $rawText .= "Kembali : Rp " . number_format($receipt['change'], 0, ',', '.') . "\n";
        }
        
        $rawText .= "Byr(Via): " . strtoupper($receipt['payment_method']) . "\n";
        $rawText .= "Status  : " . strtoupper($receipt['status']) . "\n";
        $rawText .= "================================\nTerima Kasih!";
        
        $encodedText = rawurlencode($rawText);
        $rawbtIntent = "intent:{$encodedText}#Intent;scheme=rawbt;package=ru.a402d.rawbtprinter;end;";
    @endphp

    <div class="no-print" style="text-align: center; margin-top: 16px; display: flex; flex-direction: column; gap: 10px; align-items: center;">
        
        <!-- Tombol Utama untuk RawBT (Silent Print via WebSocket) -->
        <button onclick="printSilentRawBT()" style="padding: 12px 24px; background: #0ea5e9; color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 15px; font-weight: bold; width: 280px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            🚀 Cetak Otomatis (RawBT)
        </button>

        <!-- Tombol Cetak Web Bluetooth API -->
        <button onclick="printWebBluetooth()" style="padding: 10px 24px; background: #8b5cf6; color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 14px; width: 280px;">
            📶 Cetak Bluetooth (Native Web)
        </button>

        <!-- Tombol Fallback Browser -->
        <button onclick="window.print()" style="padding: 10px 24px; background: #16a34a; color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 14px; width: 280px;">
            🖨️ Cetak via Browser (PDF)
        </button>

        <!-- Tombol Tutup -->
        <button onclick="window.close()" style="padding: 10px 24px; background: #6b7280; color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 14px; width: 280px;">
            ✕ Tutup Halaman
        </button>
    </div>

    <!-- Hidden Raw Text -->
    <textarea id="rawReceiptText" style="opacity: 0; position: absolute; z-index: -1;">{{ $rawText }}</textarea>

    <script>
        // Auto print on load if ?autoprint=1
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('autoprint') === '1') {
            window.onload = () => setTimeout(() => printSilentRawBT(), 500);
        }

        function printSilentRawBT() {
            const rawText = document.getElementById('rawReceiptText').value;
            const socket = new WebSocket("ws://127.0.0.1:40213/");
            
            socket.onopen = function() {
                socket.send(rawText);
                socket.close();
            };
            
            socket.onerror = function(error) {
                window.location.href = "{{ $rawbtIntent }}";
            };
        }

        async function printWebBluetooth() {
            if (!navigator.bluetooth) {
                alert("Web Bluetooth API tidak didukung di browser ini, ATAU Anda sedang tidak menggunakan koneksi aman (HTTPS). Silakan akses via HTTPS/ngrok.");
                return;
            }

            try {
                let device = null;

                // Coba ambil device yang sudah pernah diizinkan sebelumnya agar tidak perlu popup lagi
                if (navigator.bluetooth.getDevices) {
                    const devices = await navigator.bluetooth.getDevices();
                    if (devices.length > 0) {
                        device = devices[0]; // Pakai printer yang terakhir kali digunakan
                        console.log('Menggunakan perangkat yang sudah tersimpan: ' + device.name);
                    }
                }

                if (!device) {
                    // Jika belum pernah pairing, minta izin ke user (memunculkan popup)
                    device = await navigator.bluetooth.requestDevice({
                        acceptAllDevices: true,
                        optionalServices: ['000018f0-0000-1000-8000-00805f9b34fb', 'e7810a71-73ae-499d-8c15-faa9aef0c3f2', '49535343-fe7d-4ae5-8fa9-9fafd205e455']
                    });
                }

                console.log('Menghubungkan ke GATT Server...');
                const server = await device.gatt.connect();

                console.log('Mencari Service Printer...');
                const services = await server.getPrimaryServices();
                let printerService = null;
                for (let service of services) {
                    if (['000018f0-0000-1000-8000-00805f9b34fb', 'e7810a71-73ae-499d-8c15-faa9aef0c3f2', '49535343-fe7d-4ae5-8fa9-9fafd205e455'].includes(service.uuid)) {
                        printerService = service;
                        break;
                    }
                }

                if (!printerService) {
                    printerService = services[0];
                }

                console.log('Mencari Characteristic untuk Write...');
                const characteristics = await printerService.getCharacteristics();
                let writeCharacteristic = characteristics.find(c => c.properties.write || c.properties.writeWithoutResponse);

                if (!writeCharacteristic) {
                    throw new Error("Tidak dapat menemukan fungsi Write pada printer ini.");
                }

                console.log('Mengonversi teks ke format ESC/POS...');
                const rawText = document.getElementById('rawReceiptText').value;
                
                // Encoder teks (Sederhana ASCII)
                const encoder = new TextEncoder();
                // ESC @ (Initialize printer) + Text + LF + LF + LF + GS V (Cut)
                const initBytes = new Uint8Array([0x1B, 0x40]);
                const textBytes = encoder.encode(rawText);
                const cutBytes = new Uint8Array([0x0A, 0x0A, 0x0A, 0x1D, 0x56, 0x41, 0x00]);

                const data = new Uint8Array(initBytes.length + textBytes.length + cutBytes.length);
                data.set(initBytes);
                data.set(textBytes, initBytes.length);
                data.set(cutBytes, initBytes.length + textBytes.length);

                console.log('Mengirim data (Chunking per 20 bytes)...');
                const CHUNK_SIZE = 20; // BLE standard limit is ~20 bytes per write
                for (let i = 0; i < data.length; i += CHUNK_SIZE) {
                    const chunk = data.slice(i, i + CHUNK_SIZE);
                    if (writeCharacteristic.properties.writeWithoutResponse) {
                        await writeCharacteristic.writeValueWithoutResponse(chunk);
                    } else {
                        await writeCharacteristic.writeValue(chunk);
                    }
                    // Delay kecil antar chunk untuk mencegah buffer printer penuh
                    await new Promise(resolve => setTimeout(resolve, 20));
                }

                console.log('Cetak berhasil!');
                device.gatt.disconnect();
                alert("Pencetakan Web Bluetooth Berhasil!");
                
            } catch (error) {
                console.error(error);
                alert("Gagal mencetak: " + error.message);
            }
        }
    </script>
</body>
</html>
