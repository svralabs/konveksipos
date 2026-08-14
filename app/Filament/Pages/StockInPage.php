<?php

namespace App\Filament\Pages;

use App\Models\Product;
use App\Models\Supplier;
use App\Models\StockLedger;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class StockInPage extends Page implements HasTable
{
    use InteractsWithTable, HasPageShield;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrow-down-tray';
    protected static string|\UnitEnum|null $navigationGroup = 'Inventaris';
    protected static ?string $navigationLabel = 'Stock In (Barang Masuk)';
    protected static ?string $title = 'Penerimaan Barang (Stock In)';
    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.stock-in-page';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('create_stock_in')
                ->label('Tambah Stok Masuk')
                ->icon('heroicon-o-plus')
                ->color('success')
                ->modalHeading('Input Penerimaan Barang (Stock In)')
                ->modalWidth('lg')
                ->form([
                    Select::make('product_id')
                        ->label('Produk')
                        ->options(Product::query()->orderBy('name')->pluck('name', 'id'))
                        ->searchable()
                        ->required()
                        ->live()
                        ->afterStateUpdated(function ($state, callable $set) {
                            if ($state) {
                                $product = Product::find($state);
                                $set('current_stock', $product?->stock ?? 0);
                            }
                        }),
                    TextInput::make('current_stock')
                        ->label('Stok Saat Ini')
                        ->numeric()
                        ->disabled()
                        ->dehydrated(false),
                    TextInput::make('qty')
                        ->label('Jumlah Masuk')
                        ->numeric()
                        ->required()
                        ->minValue(1),
                    Select::make('supplier_id')
                        ->label('Supplier (Opsional)')
                        ->options(Supplier::orderBy('name')->pluck('name', 'id'))
                        ->searchable()
                        ->nullable(),
                    Textarea::make('note')
                        ->label('Catatan / Keterangan')
                        ->columnSpanFull(),
                ])
                ->action(function (array $data) {
                    $product = Product::findOrFail($data['product_id']);
                    $qty = (int) $data['qty'];

                    DB::transaction(function () use ($product, $qty, $data) {
                        $note = $data['note'] ?? '';
                        if (!empty($data['supplier_id'])) {
                            $supplier = Supplier::find($data['supplier_id']);
                            $note = 'Dari Supplier: ' . ($supplier?->name ?? '') . ($note ? ' | ' . $note : '');
                        }

                        $product->adjustStock(
                            qty: $qty,
                            type: 'in',
                            note: $note,
                            userId: auth()->id(),
                        );
                    });

                    Notification::make()
                        ->title('Stok Berhasil Ditambahkan!')
                        ->body("Stok {$product->name} bertambah {$qty} unit.")
                        ->success()
                        ->send();
                }),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(StockLedger::query())
            ->columns([
                TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                TextColumn::make('product.name')
                    ->label('Produk')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('qty')
                    ->label('Qty Masuk')
                    ->badge()
                    ->color('success')
                    ->formatStateUsing(fn ($state) => "+{$state}")
                    ->sortable(),
                TextColumn::make('stock_before')
                    ->label('Stok Sebelum')
                    ->sortable(),
                TextColumn::make('stock_after')
                    ->label('Stok Sesudah')
                    ->sortable(),
                TextColumn::make('note')
                    ->label('Catatan / Supplier')
                    ->default('-')
                    ->searchable()
                    ->limit(40),
                TextColumn::make('user.name')
                    ->label('Petugas')
                    ->default('-')
                    ->searchable()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(function ($query) {
                $filter = \App\Livewire\GlobalHeader::getActiveDateRange();
                return $query->where('type', 'in')
                    ->whereBetween('created_at', [
                        $filter['start'] . ' 00:00:00',
                        $filter['end']   . ' 23:59:59',
                    ]);
            })
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('product')
                    ->label('Produk')
                    ->relationship('product', 'name')
                    ->searchable(),
                \Filament\Tables\Filters\SelectFilter::make('user')
                    ->label('Petugas')
                    ->relationship('user', 'name')
                    ->searchable(),
            ])
            ->actions([])
            ->bulkActions([]);
    }
}
