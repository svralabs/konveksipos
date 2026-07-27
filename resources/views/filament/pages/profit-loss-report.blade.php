<x-filament-panels::page>
    <div class="flex flex-col gap-4">
        
        <!-- Deficit Alert Banner (Shown when Net Profit < 0) -->
        @if($isDeficit)
        <div class="p-3.5 sm:p-4 bg-rose-50 dark:bg-rose-950/70 border border-rose-200 dark:border-rose-900/80 rounded-xl shadow-2xs flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 text-rose-900 dark:text-rose-100">
            <div class="flex items-start sm:items-center gap-3">
                <div class="p-2 bg-rose-500 text-white rounded-lg shadow-2xs shrink-0 mt-0.5 sm:mt-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                    </svg>
                </div>
                <div class="space-y-0.5">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-rose-700 dark:text-rose-300">
                        PERHATIAN: OPERASIONAL MEMILIKI DEFISIT / RUGI NETT (Rp {{ number_format($netProfit, 0, ',', '.') }})
                    </h4>
                    <p class="text-[11px] sm:text-xs font-medium text-rose-600 dark:text-rose-400 leading-relaxed">
                        Total HPP modal produk (Rp {{ number_format($cogs, 0, ',', '.') }}) dan biaya operasional (Rp {{ number_format($expenses, 0, ',', '.') }}) melebihi pendapatan penjualan. Margin: <strong class="underline font-bold">{{ number_format($netMargin, 1) }}%</strong>.
                    </p>
                </div>
            </div>
            <div class="shrink-0 self-start sm:self-center">
                <span class="inline-flex items-center text-[10px] font-bold uppercase tracking-wider bg-rose-200/80 dark:bg-rose-900/90 text-rose-900 dark:text-rose-100 px-2.5 py-1 rounded-full border border-rose-300 dark:border-rose-700">
                    Defisit Operasional
                </span>
            </div>
        </div>
        @endif

        <!-- Main Content Container Card -->
        <div class="p-4 sm:p-6 bg-white dark:bg-zinc-900 rounded-xl border border-slate-200 dark:border-zinc-800 shadow-2xs space-y-5 sm:space-y-6">
            
            <!-- Clean Header Period Badge -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-100 dark:border-zinc-800">
                <div class="flex items-center gap-2.5">
                    <div class="w-2.5 h-6 bg-emerald-500 rounded-full shrink-0"></div>
                    <div>
                        <h3 class="text-base sm:text-lg font-bold text-slate-800 dark:text-zinc-100 tracking-tight">Ringkasan Laporan Keuangan</h3>
                        <p class="text-[11px] sm:text-xs font-medium text-slate-500 dark:text-zinc-400 mt-0.5">
                            Periode: <span class="font-bold text-emerald-600 dark:text-emerald-400">{{ \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') }}</span> s/d <span class="font-bold text-emerald-600 dark:text-emerald-400">{{ \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') }}</span>
                        </p>
                    </div>
                </div>
                <div class="self-start sm:self-auto sm:text-right bg-slate-50 dark:bg-zinc-800/80 px-3 py-1.5 rounded-lg border border-slate-200/80 dark:border-zinc-700/80">
                    <span class="text-[9px] font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-wider block">Status Akhir Periode</span>
                    <span class="text-xs font-bold {{ $netProfit >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                        {{ $netProfit >= 0 ? 'SURPLUS NETT' : 'DEFISIT NETT' }}
                    </span>
                </div>
            </div>

            <!-- Executive Quick KPI Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                <div class="p-3.5 sm:p-4 bg-slate-50 dark:bg-zinc-800/80 rounded-xl border border-slate-200/80 dark:border-zinc-700/80 flex flex-col justify-between h-full space-y-2">
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-[10px] font-bold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">Total Pendapatan</span>
                        <div class="p-1.5 bg-emerald-100 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400 rounded-lg shrink-0">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-6h6m-7 8h8a2 2 0 002-2V6a2 2 0 00-2-2H7a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                    </div>
                    <div>
                        <p class="text-base sm:text-lg font-bold text-slate-800 dark:text-zinc-100 font-mono tracking-tight break-words">Rp {{ number_format($revenue, 0, ',', '.') }}</p>
                        <span class="text-[11px] text-slate-500 dark:text-zinc-400 block font-medium mt-0.5">{{ $totalOrdersCount }} Transaksi</span>
                    </div>
                </div>

                <div class="p-3.5 sm:p-4 bg-slate-50 dark:bg-zinc-800/80 rounded-xl border border-slate-200/80 dark:border-zinc-700/80 flex flex-col justify-between h-full space-y-2">
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-[10px] font-bold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">Total HPP (Modal)</span>
                        <div class="p-1.5 bg-rose-100 dark:bg-rose-950 text-rose-600 dark:text-rose-400 rounded-lg shrink-0">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z"/><path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z"/></svg>
                        </div>
                    </div>
                    <div>
                        <p class="text-base sm:text-lg font-bold text-rose-600 dark:text-rose-400 font-mono tracking-tight break-words">Rp {{ number_format($cogs, 0, ',', '.') }}</p>
                        <span class="text-[11px] text-slate-500 dark:text-zinc-400 block font-medium mt-0.5">{{ count($cogsProductBreakdown) }} Produk Terjual</span>
                    </div>
                </div>

                <div class="p-3.5 sm:p-4 bg-slate-50 dark:bg-zinc-800/80 rounded-xl border border-slate-200/80 dark:border-zinc-700/80 flex flex-col justify-between h-full space-y-2">
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-[10px] font-bold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">Biaya Operasional</span>
                        <div class="p-1.5 bg-amber-100 dark:bg-amber-950 text-amber-600 dark:text-amber-400 rounded-lg shrink-0">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5h16.5a1.5 1.5 0 011.5 1.5v9.75a1.5 1.5 0 01-1.5 1.5H3.75a1.5 1.5 0 01-1.5-1.5V6a1.5 1.5 0 011.5-1.5z"/></svg>
                        </div>
                    </div>
                    <div>
                        <p class="text-base sm:text-lg font-bold text-amber-600 dark:text-amber-400 font-mono tracking-tight break-words">Rp {{ number_format($expenses, 0, ',', '.') }}</p>
                        <span class="text-[11px] text-slate-500 dark:text-zinc-400 block font-medium mt-0.5">{{ count($expensesCategoryBreakdown) }} Kategori</span>
                    </div>
                </div>

                <div class="p-3.5 sm:p-4 {{ $netProfit >= 0 ? 'bg-emerald-50 dark:bg-emerald-950/80 border-emerald-200 dark:border-emerald-800' : 'bg-rose-50 dark:bg-rose-950/80 border-rose-200 dark:border-rose-800' }} rounded-xl border flex flex-col justify-between h-full space-y-2">
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-[10px] font-bold {{ $netProfit >= 0 ? 'text-emerald-700 dark:text-emerald-300' : 'text-rose-700 dark:text-rose-300' }} uppercase tracking-wider">Laba Bersih</span>
                        <div class="p-1.5 {{ $netProfit >= 0 ? 'bg-emerald-200/60 dark:bg-emerald-900 text-emerald-700 dark:text-emerald-300' : 'bg-rose-200/60 dark:bg-rose-900 text-rose-700 dark:text-rose-300' }} rounded-lg shrink-0">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/></svg>
                        </div>
                    </div>
                    <div>
                        <p class="text-base sm:text-lg font-bold {{ $netProfit >= 0 ? 'text-emerald-700 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }} font-mono tracking-tight break-words">Rp {{ number_format($netProfit, 0, ',', '.') }}</p>
                        <span class="text-[11px] font-semibold {{ $netProfit >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }} block mt-0.5">Margin: {{ number_format($netMargin, 1) }}%</span>
                    </div>
                </div>
            </div>

            <!-- WATERFALL FLOW TIMELINE CONTAINER -->
            <div class="relative pl-7 sm:pl-10 space-y-5 sm:space-y-6 my-2">
                <!-- Continuous Vertical Timeline Line -->
                <div class="absolute left-[11px] sm:left-[15px] top-3 bottom-3 w-0.5 bg-slate-200 dark:bg-zinc-700"></div>

                <!-- STEP 1: PENDAPATAN (PLUS +) -->
                <div class="relative space-y-3">
                    <!-- Step Circle Badge -->
                    <div class="absolute -left-7 sm:-left-9 top-3 w-6 h-6 bg-emerald-500 text-white rounded-full flex items-center justify-center text-xs font-bold shadow-sm ring-4 ring-white dark:ring-zinc-900 z-10">
                        +
                    </div>

                    <!-- Step Header Box -->
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 p-3 sm:p-4 bg-emerald-500/10 dark:bg-emerald-950/40 rounded-xl border border-emerald-200 dark:border-emerald-900/60 shadow-2xs">
                        <div class="space-y-0.5">
                            <span class="text-[10px] font-bold uppercase text-emerald-600 dark:text-emerald-400 tracking-wider">LANGKAH 1 — PENDAPATAN PENJUALAN</span>
                            <h3 class="text-sm sm:text-base font-bold text-slate-800 dark:text-zinc-100">Total Omset Penjualan</h3>
                            <p class="text-[11px] sm:text-xs text-slate-500 dark:text-zinc-400">Dari {{ $totalOrdersCount }} transaksi ({{ $completedOrdersCount }} lunas, {{ $piutangOrdersCount }} tempo)</p>
                        </div>
                        <div class="shrink-0 self-start md:self-auto text-left md:text-right">
                            <span class="text-base sm:text-lg font-bold text-emerald-600 dark:text-emerald-400 font-mono">+ Rp {{ number_format($revenue, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <!-- Breakdown Sub-cards Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2 sm:gap-2.5 pt-0.5">
                        @foreach($revenueByPayment as $pay)
                        @php $pct = $revenue > 0 ? ($pay['amount'] / $revenue) * 100 : 0; @endphp
                        <div class="p-2.5 sm:p-3 bg-slate-50 dark:bg-zinc-800/90 rounded-lg border border-slate-200/80 dark:border-zinc-700/80 flex flex-col justify-between h-full space-y-2">
                            <div class="flex items-start justify-between gap-2">
                                <span class="text-xs font-semibold text-slate-800 dark:text-zinc-100 break-words">{{ $pay['label'] }}</span>
                                <span class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-100 dark:bg-emerald-950/80 px-2 py-0.5 rounded-full shrink-0">{{ number_format($pct, 1) }}%</span>
                            </div>
                            <div class="space-y-1">
                                <div class="flex items-baseline justify-between text-xs">
                                    <span class="text-[11px] text-slate-500 dark:text-zinc-400 font-medium">{{ $pay['count'] }} Transaksi</span>
                                    <span class="font-bold text-slate-800 dark:text-zinc-100 font-mono text-xs">Rp {{ number_format($pay['amount'], 0, ',', '.') }}</span>
                                </div>
                                <div class="w-full bg-slate-200 dark:bg-zinc-700 h-1 rounded-full overflow-hidden">
                                    <div class="bg-emerald-500 h-full rounded-full" style="width: {{ $pct }}%"></div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- STEP 2: HARGA POKOK PENJUALAN (MINUS -) -->
                <div class="relative space-y-3">
                    <!-- Step Circle Badge -->
                    <div class="absolute -left-7 sm:-left-9 top-3 w-6 h-6 bg-rose-500 text-white rounded-full flex items-center justify-center text-xs font-bold shadow-sm ring-4 ring-white dark:ring-zinc-900 z-10">
                        -
                    </div>

                    <!-- Step Header Box -->
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 p-3 sm:p-4 bg-rose-500/10 dark:bg-rose-950/40 rounded-xl border border-rose-200 dark:border-rose-900/60 shadow-2xs">
                        <div class="space-y-0.5">
                            <span class="text-[10px] font-bold uppercase text-rose-600 dark:text-rose-400 tracking-wider">LANGKAH 2 — HARGA POKOK PENJUALAN (HPP)</span>
                            <h3 class="text-sm sm:text-base font-bold text-slate-800 dark:text-zinc-100">Total Modal Produk Terjual</h3>
                            <p class="text-[11px] sm:text-xs text-slate-500 dark:text-zinc-400">Total beban modal bahan baku & produk terjual</p>
                        </div>
                        <div class="shrink-0 self-start md:self-auto text-left md:text-right">
                            <span class="text-base sm:text-lg font-bold text-rose-600 dark:text-rose-400 font-mono">- Rp {{ number_format($cogs, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <!-- Breakdown Sub-cards Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2 sm:gap-2.5 pt-0.5">
                        @foreach($cogsProductBreakdown as $prod)
                        <div class="p-2.5 sm:p-3 bg-slate-50 dark:bg-zinc-800/90 rounded-lg border border-slate-200/80 dark:border-zinc-700/80 flex flex-col justify-between h-full space-y-2">
                            <div class="flex items-start justify-between gap-2">
                                <span class="text-xs font-semibold text-slate-800 dark:text-zinc-100 line-clamp-2 leading-snug">{{ $prod['name'] }}</span>
                                <span class="text-[10px] font-bold text-rose-600 dark:text-rose-400 bg-rose-100 dark:bg-rose-950/80 px-2 py-0.5 rounded-full shrink-0">{{ number_format($prod['percentage'], 1) }}%</span>
                            </div>
                            <div class="space-y-1">
                                <div class="flex items-baseline justify-between text-xs">
                                    <span class="text-[11px] text-slate-500 dark:text-zinc-400 font-medium">{{ $prod['qty'] }} unit</span>
                                    <span class="font-bold text-rose-600 dark:text-rose-400 font-mono text-xs">Rp {{ number_format($prod['total_cogs'], 0, ',', '.') }}</span>
                                </div>
                                <div class="w-full bg-slate-200 dark:bg-zinc-700 h-1 rounded-full overflow-hidden">
                                    <div class="bg-rose-500 h-full rounded-full" style="width: {{ $prod['percentage'] }}%"></div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- WATERFALL RESULT 1: LABA KOTOR (GROSS PROFIT) -->
                <div class="relative space-y-2">
                    <!-- Step Circle Badge -->
                    <div class="absolute -left-7 sm:-left-9 top-1/2 -translate-y-1/2 w-6 h-6 bg-slate-700 dark:bg-zinc-300 text-white dark:text-slate-900 rounded-full flex items-center justify-center text-xs font-bold shadow-sm ring-4 ring-white dark:ring-zinc-900 z-10">
                        =
                    </div>

                    <div class="p-3 sm:p-4 bg-slate-100 dark:bg-zinc-800/90 rounded-xl border border-slate-300 dark:border-zinc-700 shadow-2xs flex flex-col md:flex-row md:items-center justify-between gap-3">
                        <div class="space-y-0.5">
                            <span class="text-[10px] font-bold uppercase text-slate-600 dark:text-zinc-300 tracking-wider">HASIL 1 — LABA KOTOR (GROSS PROFIT)</span>
                            <h4 class="text-xs sm:text-sm font-bold text-slate-800 dark:text-zinc-100">Pendapatan dikurangi HPP Modal Produk</h4>
                        </div>
                        <div class="shrink-0 self-start md:self-auto text-left md:text-right">
                            <span class="text-base sm:text-lg font-bold {{ $grossProfit < 0 ? 'text-rose-600 dark:text-rose-400' : 'text-emerald-600 dark:text-emerald-400' }} font-mono block">
                                Rp {{ number_format($grossProfit, 0, ',', '.') }}
                            </span>
                            <span class="text-[11px] font-semibold text-slate-500 dark:text-zinc-400 block mt-0.5">Gross Margin: {{ number_format($grossMargin, 1) }}%</span>
                        </div>
                    </div>
                </div>

                <!-- STEP 3: BIAYA OPERASIONAL (MINUS -) -->
                <div class="relative space-y-3">
                    <!-- Step Circle Badge -->
                    <div class="absolute -left-7 sm:-left-9 top-3 w-6 h-6 bg-amber-500 text-white rounded-full flex items-center justify-center text-xs font-bold shadow-sm ring-4 ring-white dark:ring-zinc-900 z-10">
                        -
                    </div>

                    <!-- Step Header Box -->
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 p-3 sm:p-4 bg-amber-500/10 dark:bg-amber-950/40 rounded-xl border border-amber-200 dark:border-amber-900/60 shadow-2xs">
                        <div class="space-y-0.5">
                            <span class="text-[10px] font-bold uppercase text-amber-600 dark:text-amber-400 tracking-wider">LANGKAH 3 — BIAYA OPERASIONAL</span>
                            <h3 class="text-sm sm:text-base font-bold text-slate-800 dark:text-zinc-100">Total Beban Operasional Toko</h3>
                            <p class="text-[11px] sm:text-xs text-slate-500 dark:text-zinc-400">Dari {{ count($expensesCategoryBreakdown) }} kategori pengeluaran</p>
                        </div>
                        <div class="shrink-0 self-start md:self-auto text-left md:text-right">
                            <span class="text-base sm:text-lg font-bold text-amber-600 dark:text-amber-400 font-mono">- Rp {{ number_format($expenses, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <!-- Breakdown Sub-cards Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2 sm:gap-2.5 pt-0.5">
                        @foreach($expensesCategoryBreakdown as $expCat)
                        @php $pctExp = $expenses > 0 ? ($expCat['amount'] / $expenses) * 100 : 0; @endphp
                        <div class="p-2.5 sm:p-3 bg-slate-50 dark:bg-zinc-800/90 rounded-lg border border-slate-200/80 dark:border-zinc-700/80 flex flex-col justify-between h-full space-y-2">
                            <div class="flex items-start justify-between gap-2">
                                <span class="text-xs font-semibold text-slate-800 dark:text-zinc-100 line-clamp-2 leading-snug">{{ $expCat['label'] }}</span>
                                <span class="text-[10px] font-bold text-amber-600 dark:text-amber-400 bg-amber-100 dark:bg-amber-950/80 px-2 py-0.5 rounded-full shrink-0">{{ number_format($pctExp, 1) }}%</span>
                            </div>
                            <div class="space-y-1">
                                <div class="flex items-baseline justify-between text-xs">
                                    <span class="text-[11px] text-slate-500 dark:text-zinc-400 font-medium">{{ $expCat['count'] }} Catatan</span>
                                    <span class="font-bold text-amber-600 dark:text-amber-400 font-mono text-xs">Rp {{ number_format($expCat['amount'], 0, ',', '.') }}</span>
                                </div>
                                <div class="w-full bg-slate-200 dark:bg-zinc-700 h-1 rounded-full overflow-hidden">
                                    <div class="bg-amber-500 h-full rounded-full" style="width: {{ $pctExp }}%"></div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- WATERFALL FINAL RESULT: LABA BERSIH (NET PROFIT) -->
                <div class="relative pt-1">
                    <!-- Step Circle Badge -->
                    <div class="absolute -left-7 sm:-left-9 top-1/2 -translate-y-1/2 w-6 h-6 {{ $netProfit >= 0 ? 'bg-emerald-600 dark:bg-emerald-500' : 'bg-rose-600 dark:bg-rose-500' }} text-white rounded-full flex items-center justify-center text-xs font-bold shadow-sm ring-4 ring-white dark:ring-zinc-900 z-10">
                        =
                    </div>

                    <!-- Final Result Banner -->
                    <div class="p-4 sm:p-5 {{ $netProfit >= 0 ? 'bg-gradient-to-br from-emerald-600 to-emerald-700 dark:from-emerald-900 dark:to-emerald-950 border-emerald-500 dark:border-emerald-700' : 'bg-gradient-to-br from-rose-600 to-rose-700 dark:from-rose-900 dark:to-rose-950 border-rose-500 dark:border-rose-700' }} rounded-xl border shadow-md flex flex-col lg:flex-row lg:items-center justify-between gap-4 text-white">
                        <div class="space-y-1.5">
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-white/20 dark:bg-white/10 text-white backdrop-blur-xs">
                                HASIL AKHIR — LABA BERSIH (NET PROFIT)
                            </span>
                            <h3 class="text-sm sm:text-base font-bold text-white">Laba Kotor dikurangi Total Biaya Operasional Toko</h3>
                            <p class="text-[11px] sm:text-xs text-white/80">
                                {{ $netProfit >= 0 ? 'Usaha mengalami keuntungan netto (Surplus) pada periode ini.' : 'Usaha mengalami kerugian netto (Defisit) pada periode ini.' }}
                            </p>
                        </div>
                        <div class="shrink-0 text-left lg:text-right bg-black/20 dark:bg-black/40 p-3.5 sm:p-4 rounded-lg border border-white/15 space-y-1">
                            <span class="text-xl sm:text-2xl font-bold tracking-tight text-white font-mono block">
                                Rp {{ number_format($netProfit, 0, ',', '.') }}
                            </span>
                            <span class="text-[11px] sm:text-xs font-bold {{ $netProfit >= 0 ? 'text-emerald-200 dark:text-emerald-300' : 'text-rose-200 dark:text-rose-300' }} block">
                                Net Profit Margin: {{ number_format($netMargin, 1) }}%
                            </span>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</x-filament-panels::page>
