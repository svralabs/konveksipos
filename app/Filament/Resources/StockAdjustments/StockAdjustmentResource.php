<?php

namespace App\Filament\Resources\StockAdjustments;

use App\Filament\Resources\StockAdjustments\Pages\ManageStockAdjustments;
use App\Models\StockAdjustment;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class StockAdjustmentResource extends Resource
{
    protected static ?string $model = StockAdjustment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedScale;
    protected static string|\UnitEnum|null $navigationGroup = 'Inventaris';
    protected static ?string $modelLabel = 'Opname & Penyesuaian Stok';
    protected static ?string $pluralModelLabel = 'Opname & Penyesuaian Stok';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\Select::make('product_id')
                    ->relationship('product', 'name')
                    ->required()
                    ->searchable()
                    ->label('Produk'),
                \Filament\Forms\Components\Select::make('type')
                    ->options([
                        'addition' => 'Penambahan (+)',
                        'subtraction' => 'Pengurangan (-)',
                        'damage' => 'Barang Rusak (-)',
                        'loss' => 'Barang Hilang (-)',
                        'opname' => 'Stock Opname (Sesuai Fisik)',
                    ])
                    ->required()
                    ->label('Tipe Penyesuaian'),
                \Filament\Forms\Components\TextInput::make('qty')
                    ->numeric()
                    ->required()
                    ->label('Kuantitas (Qty)'),
                \Filament\Forms\Components\Textarea::make('remarks')
                    ->label('Catatan Tambahan')
                    ->columnSpanFull(),
                \Filament\Forms\Components\Hidden::make('user_id')
                    ->default(auth()->id()),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('product.name')
                    ->label('Produk')
                    ->searchable()
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'addition' => 'Penambahan',
                        'subtraction' => 'Pengurangan',
                        'damage' => 'Rusak',
                        'loss' => 'Hilang',
                        'opname' => 'Opname',
                    })
                    ->color(fn (string $state): string => match($state) {
                        'addition' => 'success',
                        'subtraction' => 'warning',
                        'damage', 'loss' => 'danger',
                        'opname' => 'info',
                    }),
                \Filament\Tables\Columns\TextColumn::make('qty')
                    ->label('Qty')
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                    }),
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
                \Filament\Tables\Filters\SelectFilter::make('type')
                    ->label('Tipe')
                    ->options([
                        'addition' => 'Penambahan (+)',
                        'subtraction' => 'Pengurangan (-)',
                        'damage' => 'Barang Rusak (-)',
                        'loss' => 'Barang Hilang (-)',
                        'opname' => 'Stock Opname',
                    ]),
                \Filament\Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Menunggu Approval',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                    ]),
            ])
            ->recordActions([
                \Filament\Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (StockAdjustment $record): bool => $record->status === 'pending')
                    ->action(function (StockAdjustment $record) {
                        \Illuminate\Support\Facades\DB::transaction(function () use ($record) {
                            $product = \App\Models\Product::where('id', $record->product_id)->lockForUpdate()->first();
                            
                            $qtyChange = 0;
                            if ($record->type === 'addition') {
                                $qtyChange = $record->qty;
                            } elseif (in_array($record->type, ['subtraction', 'damage', 'loss'])) {
                                $qtyChange = -$record->qty;
                            } elseif ($record->type === 'opname') {
                                $qtyChange = $record->qty - $product->stock;
                            }

                            if ($qtyChange != 0) {
                                $typeMap = [
                                    'addition' => 'in',
                                    'subtraction' => 'out',
                                    'damage' => 'out',
                                    'loss' => 'out',
                                    'opname' => ($qtyChange > 0 ? 'in' : 'out')
                                ];
                                $product->adjustStock(
                                    qty: $qtyChange,
                                    type: $typeMap[$record->type],
                                    reference: "Adjustment #{$record->id} - {$record->remarks}",
                                    userId: auth()->id()
                                );
                            }

                            // If loss or damage, log expense based on product cogs/selling_price
                            if (in_array($record->type, ['damage', 'loss'])) {
                                \App\Models\Expense::create([
                                    'user_id' => auth()->id(),
                                    'expense_date' => now(),
                                    'amount' => $product->selling_price * $record->qty, // ideally cogs, but using selling_price for now if cogs is unavailable
                                    'category' => 'Kerugian Barang',
                                    'description' => "Barang " . ($record->type === 'loss' ? 'hilang' : 'rusak') . " - {$product->name} ({$record->qty})",
                                ]);
                            }

                            $record->update(['status' => 'approved']);
                        });
                        \Filament\Notifications\Notification::make()->title('Approved')->success()->send();
                    }),
                \Filament\Actions\Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (StockAdjustment $record): bool => $record->status === 'pending')
                    ->action(function (StockAdjustment $record) {
                        $record->update(['status' => 'rejected']);
                        \Filament\Notifications\Notification::make()->title('Rejected')->success()->send();
                    }),
                ViewAction::make(),
            ])
            ->toolbarActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageStockAdjustments::route('/'),
        ];
    }
}
