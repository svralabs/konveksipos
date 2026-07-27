<div class="space-y-6 -mt-3">
    <!-- Inline Custom Styles for Striped Chart and Layout Elements -->
    <style>
        .chart-bar-striped {
            background: repeating-linear-gradient(
                -45deg,
                #cbd5e1,
                #cbd5e1 6px,
                #f1f5f9 6px,
                #f1f5f9 12px
            ) !important;
        }
        .chart-bar-striped-dark {
            background: repeating-linear-gradient(
                -45deg,
                #065f46,
                #065f46 6px,
                #064e3b 6px,
                #064e3b 12px
            ) !important;
        }
        .time-tracker-card {
            background: radial-gradient(circle at top right, #064e3b, #022c22);
            position: relative;
            overflow: hidden;
        }
        .time-tracker-card::after {
            content: '';
            position: absolute;
            top: -20%;
            right: -20%;
            width: 180px;
            height: 180px;
            border-radius: 9999px;
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.15), rgba(4, 120, 87, 0));
            pointer-events: none;
        }
    </style>

    <!-- Reusable Global Header -->
    <x-global-header :show-search="true" :show-date-filter="true" />

    <!-- Dashboard Main Title Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 py-2">
        <div class="space-y-1">
            <h1 class="text-2xl font-bold text-slate-900 dark:text-zinc-100 tracking-tight">Dashboard</h1>
            <p class="text-xs text-slate-500 dark:text-zinc-400 font-medium">Pantau, koordinasi, dan selesaikan pesanan konveksi Anda dengan mudah.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="/admin/pos-kasir" class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-800 hover:bg-emerald-900 dark:bg-emerald-700 dark:hover:bg-emerald-600 text-white rounded-full text-xs font-semibold shadow-xs transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Kasir POS Baru
            </a>
            <a href="/admin/orders" class="inline-flex items-center gap-1.5 px-4 py-2 bg-white hover:bg-slate-50 dark:bg-zinc-800 dark:hover:bg-zinc-700/80 text-slate-700 dark:text-zinc-200 border border-slate-200 dark:border-zinc-700 rounded-full text-xs font-semibold shadow-xs transition-colors">
                Riwayat Transaksi
            </a>
        </div>
    </div>

    <x-filament-widgets::widgets :widgets="$this->getHeaderWidgets()" />

    <!-- Donezo-style 4 Stats Card Row -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Stat Card 1: Total Orders (Filled Emerald Dark Green Accent Card) -->
        <div class="p-5 bg-emerald-800 dark:bg-emerald-900 text-white rounded-2xl border border-emerald-900/10 shadow-xs flex flex-col justify-between h-36 relative overflow-hidden group">
            <div class="flex justify-between items-start">
                <span class="text-xs font-bold text-emerald-100/90 tracking-wide">Total Pesanan</span>
                <div class="w-7 h-7 bg-white/10 dark:bg-white/5 rounded-full flex items-center justify-center text-white border border-white/10 group-hover:scale-110 transition duration-300">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 19.5l15-15m0 0H8.25m11.25 0v11.25"/></svg>
                </div>
            </div>
            <div class="space-y-1 z-10">
                <h3 class="text-3xl font-extrabold tracking-tight">{{ $stats['total_orders'] }}</h3>
                <span class="inline-flex items-center gap-1 text-[10px] font-semibold bg-white/10 px-2 py-0.5 rounded-md text-emerald-100">
                    <span class="w-1.5 h-1.5 bg-emerald-300 rounded-full"></span>
                    {{ $filterLabel }}
                </span>
            </div>
            <!-- Decorative Background Circle -->
            <div class="absolute -right-4 -bottom-4 w-16 h-16 bg-white/5 rounded-full blur-md"></div>
        </div>

        <!-- Stat Card 2: Lunas/Selesai -->
        <div class="p-5 bg-white dark:bg-zinc-900 text-slate-800 dark:text-zinc-100 rounded-2xl border border-slate-100 dark:border-zinc-800 shadow-xs flex flex-col justify-between h-36 relative overflow-hidden group">
            <div class="flex justify-between items-start">
                <span class="text-xs font-bold text-slate-400 dark:text-zinc-500 tracking-wide">Pesanan Lunas</span>
                <div class="w-7 h-7 bg-slate-50 dark:bg-zinc-800 rounded-full flex items-center justify-center text-slate-500 dark:text-zinc-400 border border-slate-200 dark:border-zinc-700 group-hover:scale-110 transition duration-300">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 19.5l15-15m0 0H8.25m11.25 0v11.25"/></svg>
                </div>
            </div>
            <div class="space-y-1">
                <h3 class="text-3xl font-extrabold tracking-tight text-slate-800 dark:text-zinc-100">{{ $stats['ended_orders'] }}</h3>
                <span class="inline-flex items-center gap-1 text-[10px] font-semibold text-emerald-600 dark:text-emerald-400">
                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span>
                    Telah diserahterimakan
                </span>
            </div>
        </div>

        <!-- Stat Card 3: Piutang / Tempo -->
        <div class="p-5 bg-white dark:bg-zinc-900 text-slate-800 dark:text-zinc-100 rounded-2xl border border-slate-100 dark:border-zinc-800 shadow-xs flex flex-col justify-between h-36 relative overflow-hidden group">
            <div class="flex justify-between items-start">
                <span class="text-xs font-bold text-slate-400 dark:text-zinc-500 tracking-wide">Pesanan Tempo</span>
                <div class="w-7 h-7 bg-slate-50 dark:bg-zinc-800 rounded-full flex items-center justify-center text-slate-500 dark:text-zinc-400 border border-slate-200 dark:border-zinc-700 group-hover:scale-110 transition duration-300">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 19.5l15-15m0 0H8.25m11.25 0v11.25"/></svg>
                </div>
            </div>
            <div class="space-y-1">
                <h3 class="text-3xl font-extrabold tracking-tight text-slate-800 dark:text-zinc-100">{{ $stats['running_orders'] }}</h3>
                <span class="inline-flex items-center gap-1 text-[10px] font-semibold text-amber-600 dark:text-amber-400">
                    <span class="w-1.5 h-1.5 bg-amber-500 rounded-full"></span>
                    Menunggu pelunasan kasir
                </span>
            </div>
        </div>

        <!-- Stat Card 4: Hari Ini -->
        <div class="p-5 bg-white dark:bg-zinc-900 text-slate-800 dark:text-zinc-100 rounded-2xl border border-slate-100 dark:border-zinc-800 shadow-xs flex flex-col justify-between h-36 relative overflow-hidden group">
            <div class="flex justify-between items-start">
                <span class="text-xs font-bold text-slate-400 dark:text-zinc-500 tracking-wide">Hari Ini</span>
                <div class="w-7 h-7 bg-slate-50 dark:bg-zinc-800 rounded-full flex items-center justify-center text-slate-500 dark:text-zinc-400 border border-slate-200 dark:border-zinc-700 group-hover:scale-110 transition duration-300">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 19.5l15-15m0 0H8.25m11.25 0v11.25"/></svg>
                </div>
            </div>
            <div class="space-y-1">
                <h3 class="text-3xl font-extrabold tracking-tight text-slate-800 dark:text-zinc-100">{{ $stats['today_orders'] }}</h3>
                <span class="inline-flex items-center gap-1 text-[10px] font-semibold text-slate-500 dark:text-zinc-400">
                    Transaksi baru hari ini
                </span>
            </div>
        </div>
    </div>

    <!-- Donezo Layout Grid: Left Content (2 Columns Span) & Right Sidebar (1 Column) -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        
        <!-- Left Section Block (Project Analytics, Reminders, Team, Progress) -->
        <div class="xl:col-span-2 space-y-6">
            
            <!-- Row 1: Project Analytics and Reminders -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Project Analytics (2 Column Span) -->
                <div class="md:col-span-2 p-5 bg-white dark:bg-zinc-900 rounded-2xl border border-slate-100 dark:border-zinc-800 shadow-xs space-y-5">
                    <div class="flex justify-between items-center">
                        <div class="space-y-0.5">
                            <h2 class="text-sm font-bold text-slate-800 dark:text-zinc-100">Grafik Omset Penjualan</h2>
                            <p class="text-[10px] text-slate-400">Performa transaksi ({{ $filterLabel }})</p>
                        </div>
                    </div>

                    <!-- Custom Visual Striped Chart Bars -->
                    <div class="flex justify-between items-end h-44 pt-6 px-2">
                        @foreach($chartData as $chartDay)
                            <div class="flex flex-col items-center flex-1 group relative">
                                <!-- Tooltip on Hover -->
                                <div class="absolute bottom-full mb-2 bg-slate-800 text-white text-[9px] font-semibold px-2 py-0.5 rounded opacity-0 group-hover:opacity-100 transition pointer-events-none z-20 whitespace-nowrap">
                                    {{ $chartDay['formatted'] }}
                                </div>
                                
                                <!-- Visual Bar Wrapper -->
                                <div class="w-8 bg-slate-50 dark:bg-zinc-800 rounded-full h-32 flex items-end overflow-hidden">
                                    <!-- Dynamic Height bar -->
                                    <div 
                                        style="height: {{ $chartDay['percentage'] }}%;"
                                        class="w-full rounded-full transition-all duration-500 
                                            {{ $loop->index % 2 == 0 ? 'bg-emerald-700' : 'chart-bar-striped' }}
                                            {{ $loop->last ? 'chart-bar-striped-dark' : '' }}
                                        "
                                    ></div>
                                </div>
                                <span class="text-[10px] font-bold text-slate-400 dark:text-zinc-500 mt-2.5 uppercase tracking-wide">{{ $chartDay['day'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Reminders (1 Column Span) -->
                <div class="p-5 bg-white dark:bg-zinc-900 rounded-2xl border border-slate-100 dark:border-zinc-800 shadow-xs flex flex-col justify-between h-full space-y-4">
                    <div class="space-y-1">
                        <h2 class="text-sm font-bold text-slate-800 dark:text-zinc-100">Pengingat Kasir</h2>
                        <p class="text-[10px] text-slate-400">Tugas operasional hari ini</p>
                    </div>

                    <div class="space-y-2">
                        @if(count($lowStockProducts) > 0)
                            <h3 class="text-sm font-bold text-rose-600 dark:text-rose-400 leading-tight">Perhatian: Stok Menipis!</h3>
                            <p class="text-[10px] text-slate-600 dark:text-slate-400 font-semibold">Ada {{ count($lowStockProducts) }} barang yang harus di-restock (Contoh: {{ $lowStockProducts->first()->name }}).</p>
                        @else
                            <h3 class="text-sm font-bold text-slate-800 dark:text-zinc-100 leading-tight">Semua Stok Aman</h3>
                            <p class="text-[10px] text-emerald-600 dark:text-emerald-400 font-semibold">Tidak ada barang yang perlu di-restock</p>
                        @endif
                    </div>

                    <!-- Action Button -->
                    <a href="/admin/stock-in-page" class="w-full py-2 bg-emerald-800 hover:bg-emerald-900 text-white rounded-xl text-xs font-semibold shadow-xs flex items-center justify-center gap-1.5 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Mulai Update
                    </a>
                </div>
            </div>

            <!-- Row 2: Team Collaboration and Project Progress -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Team Collaboration (2 Column Span) -->
                <div class="md:col-span-2 p-5 bg-white dark:bg-zinc-900 rounded-2xl border border-slate-100 dark:border-zinc-800 shadow-xs space-y-4">
                    <div class="flex justify-between items-center">
                        <div class="space-y-0.5">
                            <h2 class="text-sm font-bold text-slate-800 dark:text-zinc-100">Tim & Staff Toko</h2>
                            <p class="text-[10px] text-slate-400">Pengelola & Operator Kasir Aktif</p>
                        </div>
                        <a href="/admin/users" class="px-2.5 py-1 bg-slate-50 hover:bg-slate-100 dark:bg-zinc-800 dark:hover:bg-zinc-700/80 border border-slate-200 dark:border-zinc-700 text-slate-600 dark:text-zinc-300 text-[10px] font-semibold rounded-full shadow-xs transition-colors">
                            + Kelola Tim
                        </a>
                    </div>

                    <!-- Team Members list -->
                    <div class="space-y-3.5">
                        @forelse($teamMembers as $member)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-full bg-slate-100 dark:bg-zinc-800 flex items-center justify-center font-bold text-xs text-slate-700 dark:text-zinc-300 border border-slate-200 dark:border-zinc-700 overflow-hidden">
                                        {{ substr($member->name, 0, 2) }}
                                    </div>
                                    <div class="text-left leading-none">
                                        <p class="text-xs font-bold text-slate-800 dark:text-zinc-200">{{ $member->name }}</p>
                                        <p class="text-[9px] text-slate-400 dark:text-zinc-500 mt-1">Operator/Staff Toko</p>
                                    </div>
                                </div>
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-bold tracking-wide bg-emerald-50 text-emerald-700 dark:bg-emerald-950/20 dark:text-emerald-400">
                                    Aktif
                                </span>
                            </div>
                        @empty
                            <p class="text-xs text-slate-400 text-center py-4">Belum ada staff terdaftar.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Project Progress Gauge Card (1 Column Span) -->
                <div class="p-5 bg-white dark:bg-zinc-900 rounded-2xl border border-slate-100 dark:border-zinc-800 shadow-xs flex flex-col justify-between items-center space-y-4">
                    <div class="w-full text-left space-y-1">
                        <h2 class="text-sm font-bold text-slate-800 dark:text-zinc-100">Efisiensi Kas</h2>
                        <p class="text-[10px] text-slate-400">Persentase Target Laba Kasir</p>
                    </div>

                    <!-- Circular Progress Gauge Component -->
                    <div class="relative flex items-center justify-center h-28 w-28">
                        @php
                            $targetPercent = $stats['revenue'] > 0 ? min(round(($stats['net_profit'] / $stats['revenue']) * 100), 100) : 0;
                            // handle case when net profit is negative
                            if ($targetPercent < 0) { $targetPercent = 0; }
                        @endphp
                        <svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36">
                            <!-- Track circular arc -->
                            <path class="text-slate-100 dark:text-zinc-800" stroke="currentColor" stroke-width="3" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            <!-- Progress circular arc -->
                            <path class="text-emerald-700" stroke-dasharray="{{ $targetPercent }}, 100" stroke="currentColor" stroke-width="3" stroke-linecap="round" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                        </svg>
                        <div class="absolute text-center leading-none">
                            <span class="text-2xl font-black text-slate-800 dark:text-zinc-100">{{ $targetPercent }}%</span>
                            <span class="block text-[8px] font-bold text-slate-400 dark:text-zinc-500 uppercase mt-0.5">Laba Bersih</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 text-[9px] font-bold text-slate-500 dark:text-zinc-400">
                        <span class="flex items-center gap-1"><span class="w-2 h-2 bg-emerald-700 rounded-full"></span> Laba</span>
                        <span class="flex items-center gap-1"><span class="w-2 h-2 bg-slate-200 dark:bg-zinc-800 rounded-full"></span> Pengeluaran</span>
                    </div>
                </div>
            </div>

        </div>

        <!-- Right Side Panel Column (Checklist of tasks & Digital Clock Timer) -->
        <div class="space-y-6">
            <!-- Checklist Card (Donezo style Projects list) -->
            <div class="bg-white dark:bg-zinc-900 p-5 rounded-2xl border border-slate-100 dark:border-zinc-800 shadow-xs space-y-4">
                <div class="flex justify-between items-center">
                    <div class="space-y-0.5">
                        <h2 class="text-sm font-bold text-slate-800 dark:text-zinc-100">Pesanan Terbaru</h2>
                        <p class="text-[10px] text-slate-400">Transaksi ({{ $filterLabel }})</p>
                    </div>
                    <a href="/admin/orders" class="px-2.5 py-1 bg-slate-50 hover:bg-slate-100 dark:bg-zinc-800 dark:hover:bg-zinc-700/80 border border-slate-200 dark:border-zinc-700 text-slate-600 dark:text-zinc-300 text-[10px] font-semibold rounded-full shadow-xs transition-colors">
                        + Baru
                    </a>
                </div>

                <!-- Checklist tasks -->
                <div class="space-y-3">
                    @forelse($recentOrders as $order)
                    <div class="flex items-start justify-between border-b border-slate-50 dark:border-zinc-800/80 pb-2">
                        <div class="space-y-1">
                            <span class="text-[10px] text-slate-400 font-semibold tracking-wider">{{ $order->receipt_number }}</span>
                            <p class="text-xs font-bold text-slate-700 dark:text-zinc-200">{{ $order->customer->name ?? 'Pelanggan Umum' }}</p>
                            <p class="text-[9px] text-slate-400">Rp {{ number_format($order->total, 0, ',', '.') }} ({{ ucfirst($order->status) }})</p>
                        </div>
                        @if($order->status === 'completed')
                        <span class="w-2 h-2 bg-emerald-500 rounded-full mt-1.5" title="Selesai"></span>
                        @elseif($order->status === 'piutang')
                        <span class="w-2 h-2 bg-amber-500 rounded-full mt-1.5" title="Tempo"></span>
                        @else
                        <span class="w-2 h-2 bg-slate-500 rounded-full mt-1.5" title="Diproses"></span>
                        @endif
                    </div>
                    @empty
                    <p class="text-xs text-slate-400">Belum ada pesanan terbaru.</p>
                    @endforelse
                </div>
            </div>

            <!-- Time Tracker / Digital Running Clock (Live browser execution via AlpineJS) -->
            <div 
                x-data="{ 
                    time: '00:00:00',
                    isTicking: true,
                    init() {
                        this.updateTime();
                        setInterval(() => {
                            if (this.isTicking) {
                                this.updateTime();
                            }
                        }, 1000);
                    },
                    updateTime() {
                        const now = new Date();
                        const h = String(now.getHours()).padStart(2, '0');
                        const m = String(now.getMinutes()).padStart(2, '0');
                        const s = String(now.getSeconds()).padStart(2, '0');
                        this.time = `${h}:${m}:${s}`;
                    }
                }"
                class="time-tracker-card p-5 rounded-2xl border border-emerald-950 text-white shadow-md flex flex-col justify-between h-44"
            >
                <div class="space-y-0.5">
                    <span class="text-[10px] font-bold text-emerald-300 uppercase tracking-widest">Jam Digital Kasir</span>
                    <h2 class="text-sm font-semibold text-emerald-100">Waktu Operasional Toko</h2>
                </div>

                <!-- Ticking clock display -->
                <div class="text-3xl font-black font-mono tracking-wider text-emerald-50 select-none py-2" x-text="time">
                    00:00:00
                </div>

                <!-- Interactive controls -->
                <div class="flex items-center gap-3">
                    <button 
                        @click="isTicking = !isTicking" 
                        class="p-1.5 bg-white/10 hover:bg-white/20 dark:bg-white/5 dark:hover:bg-white/10 rounded-full border border-white/20 transition-all flex items-center justify-center"
                        title="Pause / Resume Ticking"
                    >
                        <!-- Play/Pause Icon depending on status -->
                        <template x-if="isTicking">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/></template>
                        </template>
                        <template x-if="!isTicking">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></template>
                        </template>
                    </button>
                    <span class="text-[9px] font-bold text-emerald-300 flex items-center gap-1 select-none">
                        <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full animate-ping" x-show="isTicking"></span>
                        Status: <span x-text="isTicking ? 'Berjalan Live' : 'Ditahan'"></span>
                    </span>
                </div>
            </div>
        </div>

    </div>
</div>
