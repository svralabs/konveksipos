<div class="overflow-x-auto rounded-xl border border-slate-200 dark:border-zinc-800 shadow-sm">
    <table class="w-full text-left text-sm">
        <thead class="bg-slate-50 dark:bg-zinc-800/50">
            <tr>
                <th class="px-4 py-3 font-semibold text-slate-500 dark:text-zinc-400 text-xs uppercase tracking-wider">Produk</th>
                <th class="px-4 py-3 font-semibold text-slate-500 dark:text-zinc-400 text-xs uppercase tracking-wider">Harga Satuan</th>
                <th class="px-4 py-3 font-semibold text-slate-500 dark:text-zinc-400 text-xs uppercase tracking-wider text-center">Qty</th>
                <th class="px-4 py-3 font-semibold text-slate-500 dark:text-zinc-400 text-xs uppercase tracking-wider text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 dark:divide-zinc-800 bg-white dark:bg-zinc-900">
            @forelse($items as $item)
            <tr class="hover:bg-slate-50 dark:hover:bg-zinc-800/50 transition-colors">
                <td class="px-4 py-3 text-slate-800 dark:text-zinc-200 font-medium">{{ $item->product?->name ?? 'Produk Terhapus' }}</td>
                <td class="px-4 py-3 text-slate-600 dark:text-zinc-300">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                <td class="px-4 py-3 text-slate-800 dark:text-zinc-200 font-bold text-center bg-slate-50/50 dark:bg-zinc-800/20">{{ $item->qty }}</td>
                <td class="px-4 py-3 text-emerald-700 dark:text-emerald-400 text-right font-bold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="px-4 py-8 text-center text-slate-400 text-xs">Belum ada item belanja</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
