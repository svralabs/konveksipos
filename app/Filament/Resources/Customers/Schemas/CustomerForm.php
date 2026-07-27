<?php

namespace App\Filament\Resources\Customers\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class CustomerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama Pelanggan')
                    ->required(),
                Select::make('type')
                    ->label('Tipe Pelanggan')
                    ->options([
                        'retail'     => 'Ritel (Eceran)',
                        'wholesale'  => 'Grosir / B2B (Pabrik)',
                    ])
                    ->default('retail')
                    ->required(),
                TextInput::make('phone')
                    ->label('No. Telepon / HP')
                    ->tel(),
                TextInput::make('email')
                    ->label('Email')
                    ->email(),
                TextInput::make('max_credit_limit')
                    ->label('Limit Kredit / Piutang Maks. (Rp)')
                    ->numeric()
                    ->prefix('Rp')
                    ->default(0)
                    ->helperText('Isi 0 jika tidak ada batasan limit kredit'),
                Textarea::make('address')
                    ->label('Alamat')
                    ->columnSpanFull(),
            ]);
    }
}
