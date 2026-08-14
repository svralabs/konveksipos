<?php

namespace App\Filament\Pages;

use App\Models\Order;
use App\Models\Expense;
use App\Livewire\GlobalHeader;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Page;

class ProfitLossReport extends Page
{
    use HasPageShield;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';
    protected static string|\UnitEnum|null $navigationGroup = 'Laporan';
    protected static ?string $title = 'Laba Rugi (Profit & Loss)';
    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.profit-loss-report';

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('exportPdf')
                ->label('Export PDF')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action('exportPdf'),
        ];
    }

    public $startDate;
    public $endDate;

    public function mount()
    {
        $filter = GlobalHeader::getActiveDateRange();
        $this->startDate = $filter['start'] ?? now()->startOfMonth()->toDateString();
        $this->endDate = $filter['end'] ?? now()->endOfMonth()->toDateString();
    }

    protected function getReportData(): array
    {
        // Re-sync with active global date filter
        $filter = GlobalHeader::getActiveDateRange();
        $this->startDate = $filter['start'] ?? $this->startDate;
        $this->endDate   = $filter['end']   ?? $this->endDate;

        // 1. Orders within date range
        $orders = Order::with(['items.product'])
            ->where('status', '!=', 'cancelled')
            ->whereBetween('created_at', [$this->startDate . ' 00:00:00', $this->endDate . ' 23:59:59'])
            ->get();

        $revenue = (float) $orders->sum('total');
        $totalOrdersCount = $orders->count();
        $completedOrdersCount = $orders->where('status', 'completed')->count();
        $piutangOrdersCount = $orders->where('status', 'piutang')->count();

        // Breakdown by payment method
        $revenueByPayment = [
            'cash'     => ['label' => 'Tunai', 'amount' => 0, 'count' => 0],
            'transfer' => ['label' => 'Transfer Bank', 'amount' => 0, 'count' => 0],
            'qris'     => ['label' => 'QRIS', 'amount' => 0, 'count' => 0],
            'debit'    => ['label' => 'Kartu Debit', 'amount' => 0, 'count' => 0],
            'piutang'  => ['label' => 'Piutang / Tempo', 'amount' => 0, 'count' => 0],
            'other'    => ['label' => 'Lainnya', 'amount' => 0, 'count' => 0],
        ];

        foreach ($orders as $order) {
            $method = strtolower($order->payment_method ?? 'cash');
            if (isset($revenueByPayment[$method])) {
                $revenueByPayment[$method]['amount'] += (float) $order->total;
                $revenueByPayment[$method]['count'] += 1;
            } else {
                $revenueByPayment['other']['amount'] += (float) $order->total;
                $revenueByPayment['other']['count'] += 1;
            }
        }

        // Filter out unused payment methods
        $revenueByPayment = array_filter($revenueByPayment, fn ($item) => $item['count'] > 0);

        // 2. HPP (COGS) Breakdown per Product
        $cogsProductBreakdown = [];
        $totalCogs = 0;

        foreach ($orders as $order) {
            foreach ($order->items as $item) {
                $productId   = $item->product_id ?? 0;
                $productName = $item->product?->name ?? 'Produk Tidak Terdaftar';
                $costPrice   = (float) ($item->product?->cost_price ?? 0);
                $qty         = (int) $item->qty;
                $subCogs     = $costPrice * $qty;

                $totalCogs += $subCogs;

                if (! isset($cogsProductBreakdown[$productId])) {
                    $cogsProductBreakdown[$productId] = [
                        'name'       => $productName,
                        'qty'        => 0,
                        'cost_price' => $costPrice,
                        'total_cogs' => 0,
                    ];
                }

                $cogsProductBreakdown[$productId]['qty'] += $qty;
                $cogsProductBreakdown[$productId]['total_cogs'] += $subCogs;
            }
        }

        // Sort products by highest total COGS
        usort($cogsProductBreakdown, fn ($a, $b) => $b['total_cogs'] <=> $a['total_cogs']);

        // Calculate percentage contribution for each product in COGS
        foreach ($cogsProductBreakdown as &$prod) {
            $prod['percentage'] = $totalCogs > 0 ? ($prod['total_cogs'] / $totalCogs) * 100 : 0;
        }
        unset($prod);

        $grossProfit = $revenue - $totalCogs;
        $grossMargin = $revenue > 0 ? ($grossProfit / $revenue) * 100 : 0;

        // 3. Operational Expenses Breakdown per Category
        $expenses = Expense::whereBetween('expense_date', [$this->startDate, $this->endDate])->get();
        $totalExpenses = (float) $expenses->sum('amount');

        $categoriesMap = Expense::categories();
        $expensesCategoryBreakdown = [];

        foreach ($expenses as $exp) {
            $catKey   = $exp->category;
            $catLabel = $categoriesMap[$catKey] ?? $catKey;

            if (! isset($expensesCategoryBreakdown[$catKey])) {
                $expensesCategoryBreakdown[$catKey] = [
                    'label'  => $catLabel,
                    'count'  => 0,
                    'amount' => 0,
                ];
            }

            $expensesCategoryBreakdown[$catKey]['count'] += 1;
            $expensesCategoryBreakdown[$catKey]['amount'] += (float) $exp->amount;
        }

        // Sort expense categories by highest amount
        usort($expensesCategoryBreakdown, fn ($a, $b) => $b['amount'] <=> $a['amount']);

        $netProfit = $grossProfit - $totalExpenses;
        $netMargin = $revenue > 0 ? ($netProfit / $revenue) * 100 : 0;
        $isDeficit = $netProfit < 0;

        return [
            'startDate'                 => $this->startDate,
            'endDate'                   => $this->endDate,
            'revenue'                   => $revenue,
            'totalOrdersCount'          => $totalOrdersCount,
            'completedOrdersCount'      => $completedOrdersCount,
            'piutangOrdersCount'        => $piutangOrdersCount,
            'revenueByPayment'          => $revenueByPayment,
            'cogs'                      => $totalCogs,
            'cogsProductBreakdown'      => $cogsProductBreakdown,
            'grossProfit'               => $grossProfit,
            'grossMargin'               => $grossMargin,
            'expenses'                  => $totalExpenses,
            'expensesCategoryBreakdown' => $expensesCategoryBreakdown,
            'netProfit'                 => $netProfit,
            'netMargin'                 => $netMargin,
            'isDeficit'                 => $isDeficit,
        ];
    }

    protected function getViewData(): array
    {
        return $this->getReportData();
    }

    public function exportPdf()
    {
        $data = $this->getReportData();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.profit-loss-pdf', $data);
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'laporan-laba-rugi-' . $this->startDate . '-to-' . $this->endDate . '.pdf');
    }
}
