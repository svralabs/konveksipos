<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('sku')
                    ->label('SKU')
                    ->required(),
                TextInput::make('name')
                    ->required(),
                Textarea::make('description')
                    ->columnSpanFull(),
                \Filament\Forms\Components\Select::make('category_id')
                    ->relationship('category', 'name'),
                \Filament\Forms\Components\Select::make('unit_id')
                    ->relationship('unit', 'name'),
                TextInput::make('cost_price')
                    ->label('Harga Modal')
                    ->required()
                    ->numeric()
                    ->prefix('Rp'),
                TextInput::make('selling_price')
                    ->label('Harga Jual')
                    ->required()
                    ->numeric()
                    ->prefix('Rp'),
                TextInput::make('stock')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
