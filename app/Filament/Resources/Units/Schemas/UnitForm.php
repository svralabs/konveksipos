<?php

namespace App\Filament\Resources\Units\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UnitForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama Satuan')
                    ->required(),
                TextInput::make('code')
                    ->label('Kode Satuan (Singkatan)')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->placeholder('contoh: KG, MTR, PCS'),
            ]);
    }
}
