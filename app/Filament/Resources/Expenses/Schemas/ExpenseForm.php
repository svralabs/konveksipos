<?php

namespace App\Filament\Resources\Expenses\Schemas;

use App\Models\Expense;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ExpenseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\Hidden::make('user_id')
                    ->default(auth()->id()),
                Select::make('category')
                    ->label('Kategori Pengeluaran')
                    ->options(Expense::categories())
                    ->default('lain-lain')
                    ->required(),
                TextInput::make('amount')
                    ->label('Jumlah (Rp)')
                    ->numeric()
                    ->prefix('Rp')
                    ->required(),
                DatePicker::make('expense_date')
                    ->label('Tanggal Pengeluaran')
                    ->default(today())
                    ->required(),
                Textarea::make('description')
                    ->label('Keterangan / Detail')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }
}
