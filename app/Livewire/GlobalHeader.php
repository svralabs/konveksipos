<?php

namespace App\Livewire;

use Livewire\Component;
use Carbon\Carbon;

class GlobalHeader extends Component
{
    public bool $showSearch = true;
    public bool $showDateFilter = true;
    public string $title = '';

    public string $datePreset = 'this_month';
    public string $customStart = '';
    public string $customEnd = '';
    public bool $showCustomModal = false;

    public function mount($showSearch = true, $showDateFilter = true, $title = '')
    {
        $this->showSearch = $showSearch;
        $this->showDateFilter = $showDateFilter;
        $this->title = $title;

        $filter = self::getActiveDateRange();
        $this->datePreset = $filter['preset'] ?? 'this_month';
        $this->customStart = $filter['start'] ?? now()->startOfMonth()->toDateString();
        $this->customEnd = $filter['end'] ?? now()->endOfMonth()->toDateString();
    }

    public static function getActiveDateRange(): array
    {
        return session('global_date_filter', [
            'preset' => 'this_month',
            'start' => now()->startOfMonth()->toDateString(),
            'end' => now()->endOfMonth()->toDateString(),
            'label' => 'Bulan Ini',
        ]);
    }

    public function setPreset(string $preset)
    {
        $start = now();
        $end = now();
        $label = 'Bulan Ini';

        switch ($preset) {
            case 'today':
                $start = now();
                $end = now();
                $label = 'Hari Ini';
                break;
            case 'last_7_days':
                $start = now()->subDays(6);
                $end = now();
                $label = '7 Hari Terakhir';
                break;
            case 'last_30_days':
                $start = now()->subDays(29);
                $end = now();
                $label = '30 Hari Terakhir';
                break;
            case 'this_month':
                $start = now()->startOfMonth();
                $end = now()->endOfMonth();
                $label = 'Bulan Ini';
                break;
            case 'last_month':
                $start = now()->subMonth()->startOfMonth();
                $end = now()->subMonth()->endOfMonth();
                $label = 'Bulan Lalu';
                break;
            case 'this_year':
                $start = now()->startOfYear();
                $end = now()->endOfYear();
                $label = 'Tahun Ini';
                break;
            case 'all_time':
                $start = \Carbon\Carbon::parse('2020-01-01');
                $end = now();
                $label = 'Semua Waktu';
                break;
        }

        return $this->saveDateRange($start->toDateString(), $end->toDateString(), $preset, $label);
    }

    public function applyCustomRange()
    {
        if (! $this->customStart || ! $this->customEnd) {
            return;
        }

        return $this->saveDateRange($this->customStart, $this->customEnd, 'custom');
    }

    public function saveDateRange(string $start, string $end, string $preset = 'custom', ?string $label = null)
    {
        if (! $label) {
            $startDate = Carbon::parse($start);
            $endDate = Carbon::parse($end);
            $label = $startDate->format('d/m/Y') . ' - ' . $endDate->format('d/m/Y');
        }

        $this->datePreset = $preset;
        $this->customStart = $start;
        $this->customEnd = $end;

        session(['global_date_filter' => [
            'preset' => $preset,
            'start' => $start,
            'end' => $end,
            'label' => $label,
        ]]);

        $this->showCustomModal = false;

        $referer = request()->header('Referer') ?? url()->current();
        $urlParts = parse_url($referer);
        $redirectUrl = $urlParts['path'] ?? url()->current();

        if (! empty($urlParts['query'])) {
            parse_str($urlParts['query'], $queryData);
            unset($queryData['tablePage'], $queryData['page']);
            if (! empty($queryData)) {
                $redirectUrl .= '?' . http_build_query($queryData);
            }
        }

        return redirect($redirectUrl);
    }

    public function render()
    {
        return view('livewire.global-header');
    }
}
