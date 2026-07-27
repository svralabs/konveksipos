<?php

namespace App\Filament\Resources\Products\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('category_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('cost_price')
                    ->label('HPP (Modal)')
                    ->money('idr')
                    ->sortable(),
                TextColumn::make('selling_price')
                    ->label('Harga Jual')
                    ->money('idr')
                    ->sortable(),
                TextColumn::make('stock')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                \Filament\Actions\Action::make('adjust_stock')
                    ->label('Penyesuaian Stok')
                    ->icon('heroicon-o-cube')
                    ->form([
                        \Filament\Forms\Components\TextInput::make('stock_adjustment')
                            ->label('Tambah / Kurangi Stok')
                            ->numeric()
                            ->required()
                            ->helperText('Gunakan angka minus (-) untuk mengurangi stok, misal: -5'),
                    ])
                    ->action(function (\App\Models\Product $record, array $data): void {
                        $record->increment('stock', $data['stock_adjustment']);
                    }),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
