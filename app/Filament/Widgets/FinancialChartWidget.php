<?php

namespace App\Filament\Widgets;

use App\Livewire\GlobalHeader;
use App\Models\GeneralLedger;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Filament\Widgets\ChartWidget;

class FinancialChartWidget extends ChartWidget
{
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';

    public function getHeading(): string
    {
        $filter = GlobalHeader::getActiveDateRange();
        $label  = $filter['label'] ?? 'Periode Ini';
        return 'Grafik Pendapatan vs Pengeluaran — ' . $label;
    }

    protected function getData(): array
    {
        $filter = GlobalHeader::getActiveDateRange();
        $start  = Carbon::parse($filter['start']);
        $end    = Carbon::parse($filter['end']);

        // Tentukan granularity berdasarkan rentang hari
        $diffDays = $start->diffInDays($end);

        $labels    = [];
        $revenues  = [];
        $expenses  = [];

        if ($diffDays <= 60) {
            // Day-by-day
            $period = CarbonPeriod::create($start, '1 day', $end);
            foreach ($period as $date) {
                $ds = $date->toDateString();
                $labels[] = $date->format('d M');

                $revenues[] = (float) GeneralLedger::whereHas('chartOfAccount', fn ($q) => $q->where('account_code', '4101'))
                    ->whereDate('transaction_date', $ds)->sum('credit');

                $expenses[] = (float) GeneralLedger::whereHas('chartOfAccount', fn ($q) => $q->where('account_code', '6101'))
                    ->whereDate('transaction_date', $ds)->sum('debit');
            }
        } elseif ($diffDays <= 180) {
            // Week-by-week
            $current = $start->copy()->startOfWeek();
            while ($current->lte($end)) {
                $weekEnd  = $current->copy()->endOfWeek()->min($end);
                $labels[] = $current->format('d M') . ' - ' . $weekEnd->format('d M');

                $revenues[] = (float) GeneralLedger::whereHas('chartOfAccount', fn ($q) => $q->where('account_code', '4101'))
                    ->whereBetween('transaction_date', [$current->toDateString(), $weekEnd->toDateString()])->sum('credit');

                $expenses[] = (float) GeneralLedger::whereHas('chartOfAccount', fn ($q) => $q->where('account_code', '6101'))
                    ->whereBetween('transaction_date', [$current->toDateString(), $weekEnd->toDateString()])->sum('debit');

                $current->addWeek();
            }
        } else {
            // Month-by-month
            $current = $start->copy()->startOfMonth();
            while ($current->lte($end)) {
                $monthEnd = $current->copy()->endOfMonth()->min($end);
                $labels[] = $current->format('M Y');

                $revenues[] = (float) GeneralLedger::whereHas('chartOfAccount', fn ($q) => $q->where('account_code', '4101'))
                    ->whereBetween('transaction_date', [$current->toDateString(), $monthEnd->toDateString()])->sum('credit');

                $expenses[] = (float) GeneralLedger::whereHas('chartOfAccount', fn ($q) => $q->where('account_code', '6101'))
                    ->whereBetween('transaction_date', [$current->toDateString(), $monthEnd->toDateString()])->sum('debit');

                $current->addMonth();
            }
        }

        return [
            'datasets' => [
                [
                    'label'           => 'Pendapatan',
                    'data'            => $revenues,
                    'borderColor'     => '#10b981',
                    'backgroundColor' => '#10b98133',
                    'fill'            => 'origin',
                ],
                [
                    'label'           => 'Pengeluaran (Operasional)',
                    'data'            => $expenses,
                    'borderColor'     => '#ef4444',
                    'backgroundColor' => '#ef444433',
                    'fill'            => 'origin',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
