<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LowStockWidget extends BaseWidget
{
    protected static ?string $heading = '⚠️ Produk Stok Hampir Habis';
    protected int|string|array $columnSpan = 'full';
    protected static ?int $sort = 3;

    public static function canView(): bool
    {
        return Product::query()
            ->whereRaw('min_stock > 0 AND stock <= min_stock')
            ->exists();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Product::query()
                    ->whereRaw('min_stock > 0 AND stock <= min_stock')
                    ->orderBy('stock')
            )
            ->columns([
                TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable(),
                TextColumn::make('name')
                    ->label('Nama Produk')
                    ->searchable(),
                TextColumn::make('category.name')
                    ->label('Kategori')
                    ->badge(),
                TextColumn::make('stock')
                    ->label('Stok Saat Ini')
                    ->badge()
                    ->color('danger'),
                TextColumn::make('min_stock')
                    ->label('Stok Minimum')
                    ->badge()
                    ->color('warning'),
            ])
            ->paginated(false);
    }
}
