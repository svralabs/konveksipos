<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class OrdersExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public function collection()
    {
        $filter = \App\Livewire\GlobalHeader::getActiveDateRange();
        return Order::with(['user', 'customer'])
            ->whereBetween('created_at', [
                $filter['start'] . ' 00:00:00',
                $filter['end']   . ' 23:59:59',
            ])
            ->latest()
            ->get();
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'No. Struk',
            'Kasir',
            'Pelanggan',
            'Subtotal (Rp)',
            'Diskon (Rp)',
            'Total (Rp)',
            'Metode Pembayaran',
            'Status Pembayaran',
        ];
    }

    public function map($row): array
    {
        return [
            $row->created_at->format('d/m/Y H:i'),
            $row->receipt_number,
            $row->user?->name ?? '-',
            $row->customer?->name ?? 'Pelanggan Umum',
            $row->subtotal,
            $row->discount,
            $row->total,
            match ($row->payment_method) {
                'cash' => 'Tunai',
                'transfer' => 'Transfer Bank',
                'qris' => 'QRIS',
                'debit' => 'Kartu Debit',
                'credit' => 'Kartu Kredit',
                'piutang' => 'Piutang / Tempo',
                default => ucfirst($row->payment_method)
            },
            $row->status === 'completed' ? 'Lunas' : 'Tempo / Piutang',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
