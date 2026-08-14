<?php

namespace App\Filament\Pages;

use App\Models\Order;
use App\Models\Expense;
use App\Models\Product;
use App\Models\Customer;
use App\Models\User;
use App\Livewire\GlobalHeader;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Page;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class DashboardCustom extends Page
{
    use HasPageShield;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-home';
    protected static ?string $title = 'Dashboard';
    protected static ?string $navigationLabel = 'Dashboard';
    protected static ?string $slug = '';

    protected string $view = 'filament.pages.dashboard-custom';

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Widgets\FinancialKpiWidget::class,
            \App\Filament\Widgets\FinancialChartWidget::class,
        ];
    }

    public function getMaxContentWidth(): \Filament\Support\Enums\Width|string|null
    {
        return \Filament\Support\Enums\Width::Full;
    }

    public array $stats = [];
    public $recentOrders = [];
    public $lowStockProducts = [];
    public $teamMembers = [];
    public array $chartData = [];
    public string $filterLabel = 'Bulan Ini';

    public function mount()
    {
        $filter = GlobalHeader::getActiveDateRange();
        $start  = $filter['start'];
        $end    = $filter['end'];
        $this->filterLabel = $filter['label'] ?? 'Periode Ini';

        $revenue = (float) Order::whereBetween('created_at', [$start . ' 00:00:00', $end . ' 23:59:59'])
            ->sum('total');

        $expenses = (float) Expense::whereBetween('expense_date', [$start, $end])
            ->sum('amount');

        $netProfit = $revenue - $expenses;

        $outstandingDebt = (float) Order::where('status', 'piutang')
            ->whereBetween('created_at', [$start . ' 00:00:00', $end . ' 23:59:59'])
            ->sum('total');

        $this->stats = [
            'total_orders'   => Order::whereBetween('created_at', [$start . ' 00:00:00', $end . ' 23:59:59'])->count(),
            'ended_orders'   => Order::where('status', 'completed')->whereBetween('created_at', [$start . ' 00:00:00', $end . ' 23:59:59'])->count(),
            'running_orders' => Order::where('status', 'piutang')->whereBetween('created_at', [$start . ' 00:00:00', $end . ' 23:59:59'])->count(),
            'today_orders'   => Order::whereDate('created_at', Carbon::today())->count(),
            'revenue'        => $revenue,
            'expenses'       => $expenses,
            'net_profit'     => $netProfit,
            'piutang'        => $outstandingDebt,
            'customers_count'=> Customer::count(),
            'products_count' => Product::count(),
        ];

        // 5 recent orders within active date range
        $this->recentOrders = Order::with(['customer'])
            ->whereBetween('created_at', [$start . ' 00:00:00', $end . ' 23:59:59'])
            ->latest()
            ->limit(5)
            ->get();

        // Low stock products (always current stock alert)
        $this->lowStockProducts = Product::with(['category'])
            ->whereRaw('min_stock > 0 AND stock <= min_stock')
            ->orderBy('stock')
            ->limit(5)
            ->get();

        // Team members
        $this->teamMembers = User::limit(4)->get();

        // Analytics chart data for active date range
        $this->chartData = $this->getAnalyticsChart($start, $end);
    }

    protected function getAnalyticsChart(string $startDateStr, string $endDateStr): array
    {
        $start = Carbon::parse($startDateStr);
        $end   = Carbon::parse($endDateStr);
        $diffDays = $start->diffInDays($end);

        $sales = [];
        $maxVal = 0;

        if ($diffDays <= 31) {
            // Daily granularity
            $days = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
            $period = CarbonPeriod::create($start, '1 day', $end);

            foreach ($period as $date) {
                $dayName  = $days[$date->dayOfWeek];
                $daySales = (float) Order::whereDate('created_at', $date->toDateString())->sum('total');

                $sales[] = [
                    'day'       => $date->format('d/m'),
                    'label'     => $dayName . ', ' . $date->format('d M'),
                    'value'     => $daySales,
                    'formatted' => 'Rp ' . number_format($daySales, 0, ',', '.')
                ];

                if ($daySales > $maxVal) {
                    $maxVal = $daySales;
                }
            }
        } else {
            // Weekly / Monthly granularity (up to 7 slices)
            $stepDays = max(1, (int) ceil($diffDays / 7));
            $current = $start->copy();

            while ($current->lte($end)) {
                $sliceEnd = $current->copy()->addDays($stepDays - 1)->min($end);
                $sliceSales = (float) Order::whereBetween('created_at', [
                    $current->toDateString() . ' 00:00:00',
                    $sliceEnd->toDateString() . ' 23:59:59'
                ])->sum('total');

                $sales[] = [
                    'day'       => $current->format('d/m'),
                    'label'     => $current->format('d M') . ' - ' . $sliceEnd->format('d M'),
                    'value'     => $sliceSales,
                    'formatted' => 'Rp ' . number_format($sliceSales, 0, ',', '.')
                ];

                if ($sliceSales > $maxVal) {
                    $maxVal = $sliceSales;
                }

                $current->addDays($stepDays);
            }
        }

        // Calculate percentage heights for visual bars
        foreach ($sales as $key => $sale) {
            $sales[$key]['percentage'] = $maxVal > 0 ? round(($sale['value'] / $maxVal) * 100) : 0;
            if ($sales[$key]['percentage'] == 0 && $sale['value'] > 0) {
                $sales[$key]['percentage'] = 10;
            }
        }

        return $sales;
    }
}
