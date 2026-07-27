<?php

namespace App\Filament\Resources\Orders\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                TextColumn::make('receipt_number')
                    ->label('No. Struk')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Kasir')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('customer.name')
                    ->label('Pelanggan')
                    ->default('Pelanggan Umum')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('total')
                    ->label('Total Belanja')
                    ->money('IDR')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('payment_method')
                    ->label('Metode Bayar')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'cash' => 'success',
                        'transfer' => 'info',
                        'qris' => 'purple',
                        'debit', 'credit' => 'sky',
                        'piutang' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'cash' => 'Tunai',
                        'transfer' => 'Transfer Bank',
                        'qris' => 'QRIS',
                        'debit' => 'Kartu Debit',
                        'credit' => 'Kartu Kredit',
                        'piutang' => 'Piutang / Tempo',
                        default => ucfirst($state),
                    })
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'completed' ? 'success' : 'danger')
                    ->formatStateUsing(fn (string $state): string => $state === 'completed' ? 'Lunas' : 'Tempo / Piutang')
                    ->searchable()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(function ($query) {
                $filter = \App\Livewire\GlobalHeader::getActiveDateRange();
                return $query->whereBetween('created_at', [
                    $filter['start'] . ' 00:00:00',
                    $filter['end']   . ' 23:59:59',
                ]);
            })
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('payment_method')
                    ->label('Metode Bayar')
                    ->options([
                        'cash' => 'Tunai',
                        'transfer' => 'Transfer Bank',
                        'qris' => 'QRIS',
                        'debit' => 'Kartu Debit',
                        'credit' => 'Kartu Kredit',
                        'piutang' => 'Piutang / Tempo',
                    ]),
                \Filament\Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'completed' => 'Lunas',
                        'piutang' => 'Tempo / Piutang',
                    ]),
            ])
            ->headerActions([
                \Filament\Actions\Action::make('export_excel')
                    ->label('Ekspor Excel')
                    ->icon('heroicon-o-document-plus')
                    ->color('success')
                    ->action(fn () => \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\OrdersExport, 'Laporan-Penjualan-' . date('Y-m-d') . '.xlsx')),
            ])
            ->recordActions([
                \Filament\Actions\Action::make('download_pdf')
                    ->label('Ekspor PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->url(fn (\App\Models\Order $record): string => route('invoice.pdf', $record))
                    ->openUrlInNewTab(),
                \Filament\Actions\ViewAction::make()
                    ->modalWidth('5xl'),
            ])
            ->toolbarActions([]);
    }
}
