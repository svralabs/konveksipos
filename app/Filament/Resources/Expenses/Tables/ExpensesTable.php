<?php

namespace App\Filament\Resources\Expenses\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ExpensesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('expense_date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('category')
                    ->label('Kategori')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => \App\Models\Expense::categories()[$state] ?? $state)
                    ->color('warning')
                    ->searchable(),
                TextColumn::make('amount')
                    ->label('Jumlah')
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Dicatat Oleh')
                    ->sortable(),
                TextColumn::make('description')
                    ->label('Keterangan')
                    ->limit(50)
                    ->searchable(),
            ])
            ->defaultSort('expense_date', 'desc')
            ->modifyQueryUsing(function ($query) {
                $filter = \App\Livewire\GlobalHeader::getActiveDateRange();
                return $query->whereBetween('expense_date', [
                    $filter['start'],
                    $filter['end'],
                ]);
            })
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('category')
                    ->label('Kategori')
                    ->options(\App\Models\Expense::categories()),
            ])
            ->headerActions([
                \Filament\Actions\Action::make('export_excel')
                    ->label('Ekspor Excel')
                    ->icon('heroicon-o-document-plus')
                    ->color('success')
                    ->action(fn () => \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\ExpensesExport, 'Laporan-Pengeluaran-' . date('Y-m-d') . '.xlsx')),
            ])
            ->recordActions([])
            ->toolbarActions([]);
    }
}
