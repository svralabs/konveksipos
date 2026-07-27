<?php

namespace App\Exports;

use App\Models\Expense;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExpensesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public function collection()
    {
        $filter = \App\Livewire\GlobalHeader::getActiveDateRange();
        return Expense::with('user')
            ->whereBetween('expense_date', [
                $filter['start'],
                $filter['end'],
            ])
            ->latest()
            ->get();
    }

    public function headings(): array
    {
        return [
            'Tanggal Pengeluaran',
            'Kategori',
            'Jumlah (Rp)',
            'Dicatat Oleh',
            'Keterangan',
        ];
    }

    public function map($row): array
    {
        return [
            $row->expense_date->format('d/m/Y'),
            Expense::categories()[$row->category] ?? $row->category,
            $row->amount,
            $row->user?->name ?? '-',
            $row->description,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
