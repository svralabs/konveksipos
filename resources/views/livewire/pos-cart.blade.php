<div>
    <style>
        /* Hide page header only on POS page to maximize workspace */
        .fi-header {
            display: none !important;
        }
        /* Minimize panel container padding to prevent scrollbar */
        .fi-main {
            padding-top: 1rem !important;
            padding-bottom: 1rem !important;
        }
        /* Lock screen height to eliminate browser scrollbar on POS page (Desktop only) */
        @media (min-width: 1025px) {
            html, body, .fi-layout, .fi-main-ctn {
                overflow: hidden !important;
                height: 100vh !important;
            }
        }
        
        .pos-container {
            display: flex;
            gap: 24px;
            width: 100%;
            height: calc(100vh - 5rem);
            align-items: stretch;
        }
        .pos-catalog {
            flex: 1;
            display: flex;
            flex-direction: column;
            height: 100%;
            overflow: hidden;
            padding-left: 8px;
            padding-right: 8px;
        }
        .pos-cart-list {
            width: 320px;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            height: 100%;
            overflow: hidden;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.03), 0 8px 10px -6px rgba(0, 0, 0, 0.03);
        }
        .dark .pos-cart-list {
            background: #18181b;
            border-color: #27272a;
        }

        .pos-checkout {
            width: 320px;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            height: 100%;
            overflow: hidden;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.03), 0 8px 10px -6px rgba(0, 0, 0, 0.03);
        }
        .dark .pos-checkout {
            background: #18181b;
            border-color: #27272a;
        }

        /* Dark mode typography utilities */
        .text-main { color: #1e293b; }
        .dark .text-main { color: #f8fafc; }
        
        .text-muted { color: #64748b; }
        .dark .text-muted { color: #94a3b8; }
        
        .text-price { color: #065f46; }
        .dark .text-price { color: #34d399; }
        
        .input-bg { background-color: #ffffff; color: #1e293b; border-color: #e2e8f0; }
        .dark .input-bg { background-color: #18181b; color: #f8fafc; border-color: #3f3f46; }
        .category-scroll {
            display: flex;
            gap: 8px;
            overflow-x: auto;
            padding: 8px 6px;
            margin-bottom: 12px;
            scrollbar-width: none; /* hide default scrollbar */
            flex-shrink: 0;
            -webkit-overflow-scrolling: touch;
        }
        .category-scroll::-webkit-scrollbar {
            display: none;
        }
        .category-pill {
            display: inline-block;
            white-space: nowrap;
            padding: 8px 18px;
            font-size: 13px;
            font-weight: 600;
            border-radius: 9999px;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            color: #64748b;
            cursor: pointer;
            transition: all 0.2s ease-in-out;
            flex-shrink: 0;
            box-shadow: 0 1px 2px rgba(0,0,0,0.01);
        }
        .dark .category-pill {
            border-color: #27272a;
            background: #202024;
            color: #a1a1aa;
        }
        .category-pill.active {
            background: #065f46 !important;
            border-color: #065f46 !important;
            color: #ffffff !important;
            box-shadow: 0 4px 6px -1px rgba(6, 95, 70, 0.2) !important;
        }
        .category-pill:hover:not(.active) {
            border-color: #065f46;
            color: #065f46;
        }
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            align-content: start;
            gap: 16px;
            overflow-y: auto;
            padding-right: 6px;
            padding-top: 12px;
            flex-grow: 1;
            padding-bottom: 24px;
        }
        .product-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 18px;
            padding: 16px;
            cursor: pointer;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 152px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.01);
            position: relative;
        }
        .dark .product-card {
            background: #18181b;
            border-color: #27272a;
        }
        .product-card:hover {
            border-color: #065f46;
            transform: translateY(-3px);
            box-shadow: 0 12px 20px -5px rgba(6, 95, 70, 0.08);
        }
        .cart-header {
            padding: 16px 20px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-shrink: 0;
            background: #ffffff;
        }
        .dark .cart-header {
            background: #18181b;
            border-bottom-color: #27272a;
        }
        .cart-items {
            flex-grow: 1;
            overflow-y: auto;
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .cart-item {
            display: flex;
            flex-direction: column;
            gap: 10px;
            padding: 14px;
            background: #f8fafc;
            border-radius: 14px;
            border: 1px solid #e2e8f0;
        }
        .dark .cart-item {
            background: #202024;
            border-color: #27272a;
        }
        .checkout-area {
            padding: 20px;
            border-top: 1px solid #e2e8f0;
            background: #ffffff;
            display: flex;
            flex-direction: column;
            gap: 14px;
            flex-shrink: 0;
            box-shadow: 0 -4px 10px -5px rgba(0,0,0,0.02);
        }
        .dark .checkout-area {
            background: #18181b;
            border-top-color: #27272a;
        }
        .input-select {
            width: 100%;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            background-color: #ffffff;
            padding: 8px 12px;
            font-size: 13px;
            font-weight: 500;
            color: #334155;
            outline: none;
            transition: all 0.2s ease-in-out;
            height: 42px;
        }
        .dark .input-select {
            border-color: #3f3f46;
            background-color: #18181b;
            color: #ffffff;
        }
        .input-select:focus {
            border-color: #065f46;
            box-shadow: 0 0 0 3px rgba(6, 95, 70, 0.15);
        }
        @media (max-width: 1024px) {
            .pos-container {
                display: flex;
                flex-direction: column;
                height: auto;
                align-items: stretch;
            }
            .pos-catalog {
                height: 500px; /* Give catalog a scrollable height */
            }
            .pos-cart-list {
                width: 100%;
                height: auto;
                min-height: 300px;
            }
            .pos-checkout {
                width: 100%;
                height: auto;
                overflow: visible;
            }
        }
    </style>

    <div class="pos-container">
        
        <!-- Left Column: Catalog -->
        <div class="pos-catalog">

            <!-- Print last receipt banner -->
            @if($lastOrderId)
            <div style="display: flex; justify-content: space-between; align-items: center; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 14px; padding: 12px 18px; margin-bottom: 12px; flex-shrink: 0;">
                <span style="font-size: 13px; font-weight: 600; color: #166534; display: flex; align-items: center; gap: 8px;">
                    <x-filament::icon icon="heroicon-o-check-circle" class="w-5 h-5 text-emerald-600"/>
                    Transaksi berhasil disimpan!
                </span>
                <a href="{{ route('receipt.print', $lastOrderId) }}" target="_blank" style="font-size: 11px; font-weight: 700; color: #ffffff; background: #065f46; padding: 8px 14px; border-radius: 9999px; text-decoration: none; display: flex; align-items: center; gap: 6px; box-shadow: 0 2px 4px rgba(6, 95, 70, 0.15);">
                    <x-filament::icon icon="heroicon-o-printer" class="w-4 h-4 text-white"/>
                    Cetak Struk
                </a>
            </div>
            @endif

            <!-- Search and Scan (Donezo Style) -->
            <div style="position: relative; margin: 16px 4px; flex-shrink: 0;">
                <span style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #94a3b8; display: flex; align-items: center;">
                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </span>
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="search" 
                    wire:keydown.enter="scanProduct"
                    placeholder="Cari produk atau scan barcode belanjaan..."
                    class="input-bg"
                    style="width: 100%; padding: 12px 100px 12px 48px; font-size: 14px; border-radius: 9999px; border-width: 1px; border-style: solid; outline: none; box-shadow: 0 2px 4px rgba(0,0,0,0.01); transition: all 0.2s; height: 46px;"
                    onfocus="this.style.borderColor='#065f46'; this.style.boxShadow='0 0 0 3px rgba(6, 95, 70, 0.12)';"
                    onblur="this.style.boxShadow='0 2px 4px rgba(0,0,0,0.01)';"
                    autofocus
                />
                <span class="input-bg" style="position: absolute; right: 16px; top: 50%; transform: translateY(-50%); font-size: 11px; font-weight: 700; color: #94a3b8; border-width: 1px; border-style: solid; padding: 2px 8px; border-radius: 8px; pointer-events: none;">
                    ⌘ F
                </span>
            </div>

            <!-- Categories Filter -->
            <div class="category-scroll">
                <button 
                    wire:click="setCategory(null)"
                    class="category-pill {{ is_null($selectedCategory) ? 'active' : '' }}"
                >
                    Semua Produk
                </button>
                
                @foreach($categories as $category)
                <button 
                    wire:click="setCategory({{ $category->id }})"
                    class="category-pill {{ $selectedCategory == $category->id ? 'active' : '' }}"
                >
                    {{ $category->name }}
                </button>
                @endforeach
            </div>

            <!-- Product Grid -->
            <div class="product-grid">
                @forelse($products as $product)
                <div wire:click="addProductById({{ $product->id }})" class="product-card">
                    <!-- Avatar & Title & SKU -->
                    <div style="display: flex; justify-content: flex-start; align-items: flex-start; gap: 12px;">
                        @php
                            $colors = ['#fef3c7', '#dbeafe', '#dcfce7', '#f3e8ff', '#ffe4e6'];
                            $textColors = ['#b45309', '#1d4ed8', '#15803d', '#7e22ce', '#be123c'];
                            $colorIndex = $product->id % count($colors);
                            $initials = collect(explode(' ', $product->name))->map(fn($w) => substr($w, 0, 1))->take(2)->join('');
                        @endphp
                        <div style="width: 42px; height: 42px; flex-shrink: 0; border-radius: 12px; background: {{ $colors[$colorIndex] }}; color: {{ $textColors[$colorIndex] }}; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 16px; text-transform: uppercase;">
                            {{ $initials }}
                        </div>
                        <div style="flex-grow: 1;">
                            <p class="text-main" style="font-size: 14px; font-weight: 700; margin-bottom: 2px; line-height: 1.35; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                {{ $product->name }}
                            </p>
                            <p class="text-muted" style="font-size: 11px; font-weight: 600; letter-spacing: 0.5px;">{{ $product->sku }}</p>
                        </div>
                    </div>
                    
                    <!-- Bottom Pricing & Stock / Add Button -->
                    <div style="display: flex; justify-content: space-between; align-items: flex-end; width: 100%;">
                        <div>
                            <!-- Smart Stock Tag -->
                            @php
                                $stock = $product->stock;
                                $stockBg = $stock <= 5 ? '#fef2f2' : ($stock <= 20 ? '#fefce8' : '#f0fdf4');
                                $stockColor = $stock <= 5 ? '#b91c1c' : ($stock <= 20 ? '#a16207' : '#166534');
                            @endphp
                            <span style="display: inline-block; background: {{ $stockBg }}; color: {{ $stockColor }}; font-weight: 700; font-size: 11px; padding: 2px 8px; border-radius: 6px; margin-bottom: 6px;">
                                Stok: {{ $stock }}
                            </span>
                            <div class="text-price" style="font-weight: 800; font-size: 15px;">
                                Rp {{ number_format($product->selling_price, 0, ',', '.') }}
                            </div>
                        </div>
                        
                        <!-- Donezo visual touch add icon -->
                        <div style="width: 34px; height: 34px; border-radius: 9999px; background: #f0fdf4; border: 1px solid #dcfce7; display: flex; align-items: center; justify-content: center; color: #065f46; transition: all 0.2s;" onmouseover="this.style.background='#065f46'; this.style.color='#ffffff';" onmouseout="this.style.background='#f0fdf4'; this.style.color='#065f46';">
                            <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"></path></svg>
                        </div>
                    </div>
                </div>
                @empty
                <div style="grid-column: 1 / -1; padding: 64px 0; text-align: center; opacity: 0.7;">
                    <x-filament::icon
                        icon="heroicon-o-magnifying-glass"
                        class="mx-auto h-12 w-12 text-slate-300 mb-2"
                    />
                    <p style="font-size: 14px; font-weight: 600; color: #64748b;">Tidak ada produk ditemukan.</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Middle Column: Cart Items -->
        <div class="pos-cart-list">
            <!-- Cart Header -->
            <div class="cart-header" style="justify-content: space-between;">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <h2 class="text-main" style="font-size: 15px; font-weight: 700; display: flex; align-items: center; gap: 8px;">
                        <svg style="width: 20px; height: 20px; color: #065f46;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"></path></svg>
                        Detail Belanja
                    </h2>
                    <span class="text-main" style="font-size: 11px; font-weight: 700; padding: 2px 10px; border-radius: 9999px; background: rgba(148, 163, 184, 0.1);">
                        {{ count($cart) }} Item
                    </span>
                </div>
                <button wire:click="promptCloseShift" style="font-size: 11px; font-weight: 700; padding: 6px 12px; border-radius: 9999px; background: #ef4444; color: white; border: none; cursor: pointer; transition: background 0.2s;" onmouseover="this.style.background='#dc2626';" onmouseout="this.style.background='#ef4444';">
                    Tutup Shift
                </button>
            </div>

            <!-- Cart Items -->
            <div class="cart-items">
                @forelse($cart as $item)
                <div class="cart-item">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 8px;">
                        <div style="flex-grow: 1;">
                            <h4 class="text-main" style="font-size: 13px; font-weight: 700; line-height: 1.35;">
                                {{ $item['name'] }}
                            </h4>
                            <p class="text-muted" style="font-size: 11px; font-weight: 600; margin-top: 4px;">@ Rp {{ number_format($item['price'], 0, ',', '.') }}</p>
                        </div>
                        <div class="text-main" style="text-align: right; font-weight: 800; font-size: 13px; white-space: nowrap;">
                            Rp {{ number_format($item['subtotal'], 0, ',', '.') }}
                        </div>
                    </div>
                    
                    <!-- Touch Friendly Buttons for Qty -->
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 6px;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <button 
                                wire:click="decrementQty({{ $item['id'] }})" 
                                style="width: 32px; height: 32px; border-radius: 9999px; background: rgba(148, 163, 184, 0.1); border: none; display: flex; align-items: center; justify-content: center; font-size: 16px; font-weight: 700; cursor: pointer; transition: all 0.2s;"
                                class="text-main"
                                onmouseover="this.style.background='rgba(148, 163, 184, 0.2)';"
                                onmouseout="this.style.background='rgba(148, 163, 184, 0.1)';"
                            >
                                -
                            </button>
                            <span class="text-main" style="width: 20px; text-align: center; font-size: 13px; font-weight: 700;">{{ $item['qty'] }}</span>
                            <button 
                                wire:click="incrementQty({{ $item['id'] }})" 
                                style="width: 32px; height: 32px; border-radius: 9999px; background: rgba(148, 163, 184, 0.1); border: none; display: flex; align-items: center; justify-content: center; font-size: 16px; font-weight: 700; cursor: pointer; transition: all 0.2s;"
                                class="text-main"
                                onmouseover="this.style.background='rgba(148, 163, 184, 0.2)';"
                                onmouseout="this.style.background='rgba(148, 163, 184, 0.1)';"
                            >
                                +
                            </button>
                        </div>
                        
                        <button wire:click="removeItem({{ $item['id'] }})" style="font-size: 11px; font-weight: 700; color: #ef4444; border: none; background: transparent; cursor: pointer; padding: 6px 10px; border-radius: 8px; transition: all 0.2s;" onmouseover="this.style.background='#fef2f2';" onmouseout="this.style.background='transparent';">
                            Hapus
                        </button>
                    </div>
                </div>
                @empty
                <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; opacity: 0.6; text-align: center; padding: 40px 0;">
                    <svg style="width: 48px; height: 48px; color: #94a3b8; margin-bottom: 8px;" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"></path></svg>
                    <p class="text-main" style="font-size: 14px; font-weight: 700;">Keranjang Kosong</p>
                    <p class="text-muted" style="font-size: 11px; margin-top: 4px;">Pilih produk di sebelah kiri</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Right Column: Checkout -->
        <div class="pos-checkout">
            <div class="cart-header">
                <h2 class="text-main" style="font-size: 15px; font-weight: 700; display: flex; align-items: center; gap: 8px;">
                    <svg style="width: 20px; height: 20px; color: #065f46;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"></path></svg>
                    Pembayaran
                </h2>
            </div>
            <!-- Checkout Area -->
            <div class="checkout-area" style="flex-grow: 1; border-top: none; display: flex; flex-direction: column; justify-content: space-between;">
                
                <div style="display: flex; flex-direction: column; gap: 14px;">
                    <!-- Customer Select -->
                    <div>
                        <label class="text-muted" style="display: block; font-size: 11px; font-weight: 700; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px;">
                            Pelanggan
                            @if($customerId)
                                @php $cust = $customers->find($customerId); @endphp
                                @if($cust)
                                    <span style="margin-left: 6px; font-size: 9px; font-weight: 700; padding: 2px 8px; border-radius: 9999px; background: {{ $cust->type === 'wholesale' ? '#fef3c7' : '#dbeafe' }}; color: {{ $cust->type === 'wholesale' ? '#b45309' : '#1d4ed8' }};">
                                        {{ $cust->type === 'wholesale' ? 'Grosir' : 'Eceran' }}
                                    </span>
                                @endif
                            @endif
                        </label>
                        <select wire:model.live="customerId" class="input-select">
                            <option value="">-- Pelanggan Umum --</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->name }} ({{ $customer->type === 'wholesale' ? 'Grosir' : 'Eceran' }})</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Payment Method -->
                    <div>
                        <label class="text-muted" style="display: block; font-size: 11px; font-weight: 700; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px;">Metode Jual</label>
                        <select wire:model.live="paymentMethod" class="input-select">
                            <option value="cash">Tunai</option>
                            <option value="transfer">Transfer Bank</option>
                            <option value="qris">QRIS</option>
                            <option value="debit">Kartu Debit</option>
                            <option value="piutang">Piutang / Tempo</option>
                        </select>
                    </div>

                    <!-- Credit Limit Warning -->
                    @if($creditLimitExceeded)
                    <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 12px; padding: 12px; font-size: 11px;">
                        <p style="font-weight: 700; color: #b91c1c; margin-bottom: 4px; display: flex; align-items: center; gap: 6px;">
                            <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Limit Kredit Terlampaui!
                        </p>
                        <p style="color: #ef4444; line-height: 1.35;">{{ $creditLimitInfo }}</p>
                    </div>
                    @endif
                </div>

                <!-- Billing Details & Button at bottom -->
                <div>
                    <div style="display: flex; flex-direction: column; gap: 10px; padding-top: 14px; border-top: 1px dashed rgba(148, 163, 184, 0.2); margin-bottom: 16px;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span class="text-muted" style="font-size: 12px; font-weight: 600;">Subtotal</span>
                            <span class="text-main" style="font-size: 13px; font-weight: 700;">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span class="text-muted" style="font-size: 12px; font-weight: 600;">Diskon (Rp)</span>
                            <input type="number" wire:model.live.debounce.500ms="discount" class="input-bg" style="width: 100px; text-align: right; padding: 6px 10px; border-radius: 8px; border-width: 1px; border-style: solid; font-size: 13px; font-weight: 700; outline: none;" onfocus="this.style.borderColor='#065f46';">
                        </div>
                        
                        @if($paymentMethod === 'cash')
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span class="text-muted" style="font-size: 12px; font-weight: 600;">Uang Dibayar</span>
                            <input type="number" wire:model.live.debounce.500ms="tenderedAmount" placeholder="0" class="input-bg" style="width: 100px; text-align: right; padding: 6px 10px; border-radius: 8px; border-width: 1px; border-style: solid; font-size: 13px; font-weight: 700; outline: none;" onfocus="this.style.borderColor='#065f46';">
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span class="text-muted" style="font-size: 12px; font-weight: 600;">Kembalian</span>
                            <span style="font-size: 13px; font-weight: 700; color: {{ $changeAmount > 0 ? '#10b981' : '#94a3b8' }};">Rp {{ number_format($changeAmount, 0, ',', '.') }}</span>
                        </div>
                        @endif

                        <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 12px; border-top: 1px dashed rgba(148, 163, 184, 0.2); margin-top: 4px;">
                            <span class="text-main" style="font-size: 15px; font-weight: 800;">Total Bayar</span>
                            <span class="text-price" style="font-size: 20px; font-weight: 800;">
                                Rp {{ number_format($total, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    <!-- Giant Emerald Green Checkout Button -->
                    <button 
                        wire:click="checkout" 
                        :disabled="empty($cart) || $creditLimitExceeded"
                        style="width: 100%; height: 50px; border-radius: 9999px; background: #065f46; color: #ffffff; border: none; font-size: 15px; font-weight: 700; display: flex; align-items: center; justify-content: center; gap: 8px; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 12px rgba(6, 95, 70, 0.2);"
                        onmouseover="this.style.background='#064e3b'; this.style.transform='translateY(-1px)';"
                        onmouseout="this.style.background='#065f46'; this.style.transform='translateY(0)';"
                    >
                        <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z"></path></svg>
                        {{ $creditLimitExceeded ? 'Limit Kredit Terlampaui' : 'Proses Pembayaran' }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Shift Modal Block (Covers Entire POS if Shift is not open) -->
    @if($shiftModalOpen)
    <div style="position: fixed; inset: 0; background: rgba(0,0,0,0.7); backdrop-filter: blur(8px); z-index: 9999; display: flex; align-items: center; justify-content: center;">
        <div style="background: #ffffff; padding: 30px; border-radius: 20px; width: 400px; max-width: 90%; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);" class="dark:bg-gray-900 dark:border-gray-800 border">
            <div style="text-align: center; margin-bottom: 24px;">
                <div style="width: 64px; height: 64px; background: #ecfdf5; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                    <svg style="width: 32px; height: 32px; color: #059669;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h2 style="font-size: 20px; font-weight: 800; color: #1e293b;" class="dark:text-white">Buka Shift Kasir</h2>
                <p style="font-size: 14px; color: #64748b; margin-top: 8px;">Silakan masukkan nominal uang modal (kembalian) awal di laci kasir Anda.</p>
            </div>
            
            <form wire:submit.prevent="openShift">
                <div style="margin-bottom: 24px;">
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 8px;" class="dark:text-gray-300">Modal Awal (Rp)</label>
                    <input type="number" wire:model="openingAmount" class="input-bg" style="width: 100%; padding: 12px 16px; border-radius: 12px; border: 1px solid #cbd5e1; font-size: 16px; font-weight: 700; outline: none; transition: border-color 0.2s;" onfocus="this.style.borderColor='#059669';" required min="0">
                    @error('openingAmount') <span style="color: #ef4444; font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span> @enderror
                </div>
                <button type="submit" style="width: 100%; padding: 14px; background: #059669; color: #ffffff; border: none; border-radius: 12px; font-size: 15px; font-weight: 700; cursor: pointer; transition: background 0.2s;" onmouseover="this.style.background='#047857';" onmouseout="this.style.background='#059669';">
                    Buka Kasir Sekarang
                </button>
            </form>
        </div>
        </div>
    </div>
    @endif

    <!-- Close Shift Modal -->
    @if($closeShiftModalOpen)
    <div style="position: fixed; inset: 0; background: rgba(0,0,0,0.7); backdrop-filter: blur(8px); z-index: 9999; display: flex; align-items: center; justify-content: center;">
        <div style="background: #ffffff; padding: 30px; border-radius: 20px; width: 400px; max-width: 90%; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);" class="dark:bg-gray-900 dark:border-gray-800 border">
            <div style="text-align: center; margin-bottom: 24px;">
                <div style="width: 64px; height: 64px; background: #fef2f2; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                    <svg style="width: 32px; height: 32px; color: #ef4444;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <h2 style="font-size: 20px; font-weight: 800; color: #1e293b;" class="dark:text-white">Tutup Shift Kasir (EOD)</h2>
                <p style="font-size: 14px; color: #64748b; margin-top: 8px;">Silakan masukkan total uang setoran akhir (termasuk modal) yang ada di laci kasir.</p>
            </div>
            
            <form wire:submit.prevent="closeShift">
                <div style="margin-bottom: 24px;">
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 8px;" class="dark:text-gray-300">Setoran Akhir (Rp)</label>
                    <input type="number" wire:model="closingAmount" class="input-bg" style="width: 100%; padding: 12px 16px; border-radius: 12px; border: 1px solid #cbd5e1; font-size: 16px; font-weight: 700; outline: none; transition: border-color 0.2s;" onfocus="this.style.borderColor='#ef4444';" required min="0">
                    @error('closingAmount') <span style="color: #ef4444; font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span> @enderror
                </div>
                
                <div style="display: flex; gap: 12px;">
                    <button type="button" wire:click="$set('closeShiftModalOpen', false)" style="flex: 1; padding: 14px; background: #e2e8f0; color: #475569; border: none; border-radius: 12px; font-size: 15px; font-weight: 700; cursor: pointer; transition: background 0.2s;" class="dark:bg-gray-800 dark:text-gray-300">
                        Batal
                    </button>
                    <button type="submit" style="flex: 1; padding: 14px; background: #ef4444; color: #ffffff; border: none; border-radius: 12px; font-size: 15px; font-weight: 700; cursor: pointer; transition: background 0.2s;" onmouseover="this.style.background='#dc2626';" onmouseout="this.style.background='#ef4444';">
                        Tutup Shift
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
