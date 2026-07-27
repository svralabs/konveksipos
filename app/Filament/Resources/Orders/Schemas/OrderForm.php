<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Transaksi')
                    ->columns(2)
                    ->schema([
                        TextInput::make('receipt_number')
                            ->label('No. Struk'),
                        \Filament\Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->label('Kasir'),
                        \Filament\Forms\Components\Select::make('customer_id')
                            ->relationship('customer', 'name')
                            ->label('Pelanggan'),
                        TextInput::make('payment_method')
                            ->label('Metode Bayar')
                            ->formatStateUsing(fn ($state) => match ($state) {
                                'cash' => 'Tunai',
                                'transfer' => 'Transfer',
                                'qris' => 'QRIS',
                                'piutang' => 'Piutang / Tempo',
                                default => $state,
                            }),
                        TextInput::make('subtotal')
                            ->prefix('Rp')
                            ->formatStateUsing(fn ($state) => number_format((float)$state, 0, ',', '.')),
                        TextInput::make('discount')
                            ->prefix('Rp')
                            ->formatStateUsing(fn ($state) => number_format((float)$state, 0, ',', '.')),
                        TextInput::make('total')
                            ->prefix('Rp')
                            ->formatStateUsing(fn ($state) => number_format((float)$state, 0, ',', '.')),
                        TextInput::make('status')
                            ->label('Status')
                            ->formatStateUsing(fn ($state) => $state === 'completed' ? 'Lunas' : 'Tempo / Piutang'),
                    ]),
                    
                Section::make('Daftar Item')
                    ->schema([
                        \Filament\Forms\Components\ViewField::make('items')
                            ->view('filament.components.order-items-table')
                            ->viewData(function (?\Illuminate\Database\Eloquent\Model $record) {
                                return [
                                    'items' => $record ? $record->items()->with('product')->get() : [],
                                ];
                            })
                            ->columnSpanFull()
                    ])
            ]);
    }
}
