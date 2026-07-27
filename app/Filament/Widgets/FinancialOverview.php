<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Expense;
use App\Livewire\GlobalHeader;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class FinancialOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $filter = GlobalHeader::getActiveDateRange();
        $start  = $filter['start'];
        $end    = $filter['end'];
        $label  = $filter['label'] ?? 'Periode Ini';

        $revenue = (float) Order::whereBetween('created_at', [$start . ' 00:00:00', $end . ' 23:59:59'])
            ->sum('total');

        $expenses = (float) Expense::whereBetween('expense_date', [$start, $end])
            ->sum('amount');

        $netProfit = $revenue - $expenses;

        $outstandingDebt = (float) Order::where('status', 'piutang')
            ->whereBetween('created_at', [$start . ' 00:00:00', $end . ' 23:59:59'])
            ->sum('total');

        return [
            Stat::make('Total Omset Penjualan', 'Rp ' . number_format($revenue, 0, ',', '.'))
                ->description($label)
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            Stat::make('Total Pengeluaran', 'Rp ' . number_format($expenses, 0, ',', '.'))
                ->description($label)
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger'),
            Stat::make('Laba Bersih', 'Rp ' . number_format($netProfit, 0, ',', '.'))
                ->description($label)
                ->descriptionIcon('heroicon-m-banknotes')
                ->color($netProfit >= 0 ? 'success' : 'danger'),
            Stat::make('Total Piutang Aktif', 'Rp ' . number_format($outstandingDebt, 0, ',', '.'))
                ->description($label)
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
        ];
    }
}
