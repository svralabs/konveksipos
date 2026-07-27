<?php

namespace App\Filament\Resources\ChartOfAccounts;

use App\Filament\Resources\ChartOfAccounts\Pages\ManageChartOfAccounts;
use App\Models\ChartOfAccount;
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

class ChartOfAccountResource extends Resource
{
    protected static ?string $model = ChartOfAccount::class;

    protected static bool $shouldRegisterNavigation = false;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;
    protected static string|\UnitEnum|null $navigationGroup = 'Keuangan';
    protected static ?string $modelLabel = 'Chart of Account (CoA)';
    protected static ?string $pluralModelLabel = 'Chart of Accounts';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\TextInput::make('account_code')
                    ->label('Kode Akun')
                    ->required()
                    ->unique(ignoreRecord: true),
                \Filament\Forms\Components\TextInput::make('name')
                    ->label('Nama Akun')
                    ->required(),
                \Filament\Forms\Components\Select::make('type')
                    ->label('Tipe Akun')
                    ->options([
                        'asset' => 'Asset (Harta)',
                        'liability' => 'Liability (Kewajiban/Utang)',
                        'equity' => 'Equity (Modal)',
                        'revenue' => 'Revenue (Pendapatan)',
                        'expense' => 'Expense (Beban/Pengeluaran)',
                    ])
                    ->required(),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Infolists\Components\TextEntry::make('account_code')->label('Kode Akun'),
                \Filament\Infolists\Components\TextEntry::make('name')->label('Nama Akun'),
                \Filament\Infolists\Components\TextEntry::make('type')->label('Tipe Akun'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('account_code')
                    ->label('Kode Akun')
                    ->searchable()
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('name')
                    ->label('Nama Akun')
                    ->searchable()
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('type')
                    ->label('Tipe Akun')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'asset' => 'Asset',
                        'liability' => 'Liability',
                        'equity' => 'Equity',
                        'revenue' => 'Revenue',
                        'expense' => 'Expense',
                    })
                    ->color(fn (string $state): string => match($state) {
                        'asset' => 'info',
                        'liability' => 'warning',
                        'equity' => 'success',
                        'revenue' => 'success',
                        'expense' => 'danger',
                    }),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageChartOfAccounts::route('/'),
        ];
    }
}
