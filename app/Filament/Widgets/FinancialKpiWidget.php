<?php

namespace App\Filament\Widgets;

use App\Livewire\GlobalHeader;
use App\Models\GeneralLedger;
use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class FinancialKpiWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $filter = GlobalHeader::getActiveDateRange();
        $start  = $filter['start'];
        $end    = $filter['end'];

        // Pendapatan (akun 4101) — dalam rentang waktu aktif
        $revenue = GeneralLedger::whereHas('chartOfAccount', fn ($q) => $q->where('account_code', '4101'))
            ->whereBetween('transaction_date', [$start, $end])
            ->sum('credit');

        // HPP (akun 5101)
        $cogs = GeneralLedger::whereHas('chartOfAccount', fn ($q) => $q->where('account_code', '5101'))
            ->whereBetween('transaction_date', [$start, $end])
            ->sum('debit');

        // Beban Operasional (akun 6101)
        $expenses = GeneralLedger::whereHas('chartOfAccount', fn ($q) => $q->where('account_code', '6101'))
            ->whereBetween('transaction_date', [$start, $end])
            ->sum('debit');

        $grossProfit = $revenue - $cogs;
        $margin      = $revenue > 0 ? ($grossProfit / $revenue) * 100 : 0;
        $netProfit   = $grossProfit - $expenses;

        // Piutang aktif (tidak difilter tanggal — menampilkan total outstanding)
        $piutang = Order::where('payment_method', 'piutang')
            ->where('status', 'piutang')
            ->sum('total');

        return [
            Stat::make('Margin Laba Kotor', number_format($margin, 2) . '%')
                ->description('Dari total pendapatan periode ini')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color($margin > 20 ? 'success' : 'warning'),

            Stat::make('Laba Bersih', 'Rp ' . number_format($netProfit, 0, ',', '.'))
                ->description('Setelah dikurangi beban')
                ->descriptionIcon($netProfit >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($netProfit >= 0 ? 'success' : 'danger'),

            Stat::make('Uang di Piutang', 'Rp ' . number_format($piutang, 0, ',', '.'))
                ->description('Tagihan B2B belum lunas')
                ->descriptionIcon('heroicon-m-clock')
                ->color('danger'),
        ];
    }
}
