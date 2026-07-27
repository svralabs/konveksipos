<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Expense;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class FinancialTrends extends ChartWidget
{
    protected ?string $heading = 'Grafik Tren Keuangan (6 Bulan Terakhir)';
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $months = [];
        $salesData = [];
        $expenseData = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $months[] = $month->translatedFormat('F Y');

            // Sales in this month
            $salesData[] = Order::query()
                ->whereMonth('created_at', $month->month)
                ->whereYear('created_at', $month->year)
                ->sum('total');

            // Expenses in this month
            $expenseData[] = Expense::query()
                ->whereMonth('expense_date', $month->month)
                ->whereYear('expense_date', $month->year)
                ->sum('amount');
        }

        return [
            'datasets' => [
                [
                    'label' => 'Omset Penjualan (Rp)',
                    'data' => $salesData,
                    'borderColor' => '#16a34a',
                    'backgroundColor' => 'rgba(22, 163, 74, 0.1)',
                    'fill' => 'start',
                ],
                [
                    'label' => 'Pengeluaran Operasional (Rp)',
                    'data' => $expenseData,
                    'borderColor' => '#dc2626',
                    'backgroundColor' => 'rgba(220, 38, 38, 0.1)',
                    'fill' => 'start',
                ],
            ],
            'labels' => $months,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
