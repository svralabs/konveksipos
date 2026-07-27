<?php

namespace App\Filament\Resources\Expenses\Widgets;

use App\Models\Expense;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class ExpenseOverviewWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getColumns(): int
    {
        return 4;
    }

    protected function getStats(): array
    {
        $filter = \App\Livewire\GlobalHeader::getActiveDateRange();
        $startDate = $filter['start'] ?? null;
        $endDate = $filter['end'] ?? null;
        $label = $filter['label'] ?? 'Bulan Ini';

        $baseQuery = Expense::query()
            ->when($startDate, fn ($q) => $q->whereDate('expense_date', '>=', $startDate))
            ->when($endDate, fn ($q) => $q->whereDate('expense_date', '<=', $endDate));

        $totalExpenses = (float) (clone $baseQuery)->sum('amount');
        $avgExpense = (float) (clone $baseQuery)->avg('amount');

        $categories = Expense::categories();

        // Kategori Terbesar
        $highest = (clone $baseQuery)->select('category', DB::raw('SUM(amount) as total_amount'))
            ->groupBy('category')
            ->orderByDesc('total_amount')
            ->first();

        $highestName = $highest && isset($categories[$highest->category]) 
            ? $categories[$highest->category] 
            : ($highest?->category ?? 'Belum Ada Data');
        $highestAmount = $highest ? (float) $highest->total_amount : 0;

        // Kategori Terkecil
        $lowest = (clone $baseQuery)->select('category', DB::raw('SUM(amount) as total_amount'))
            ->groupBy('category')
            ->orderBy('total_amount')
            ->first();

        $lowestName = $lowest && isset($categories[$lowest->category]) 
            ? $categories[$lowest->category] 
            : ($lowest?->category ?? 'Belum Ada Data');
        $lowestAmount = $lowest ? (float) $lowest->total_amount : 0;

        return [
            Stat::make('Total Pengeluaran', 'Rp ' . number_format($totalExpenses, 0, ',', '.'))
                ->description('Periode: ' . $label)
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('danger'),
            Stat::make('Kategori Terbesar: ' . $highestName, 'Rp ' . number_format($highestAmount, 0, ',', '.'))
                ->description('Periode: ' . $label)
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('warning'),
            Stat::make('Kategori Terkecil: ' . $lowestName, 'Rp ' . number_format($lowestAmount, 0, ',', '.'))
                ->description('Periode: ' . $label)
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('success'),
            Stat::make('Rata-rata Pengeluaran', 'Rp ' . number_format($avgExpense, 0, ',', '.'))
                ->description('Periode: ' . $label)
                ->descriptionIcon('heroicon-m-calculator')
                ->color('info'),
        ];
    }
}
