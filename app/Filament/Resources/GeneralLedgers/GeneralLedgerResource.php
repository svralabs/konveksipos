<?php

namespace App\Filament\Resources\GeneralLedgers;

use App\Filament\Resources\GeneralLedgers\Pages\ManageGeneralLedgers;
use App\Models\GeneralLedger;
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

class GeneralLedgerResource extends Resource
{
    protected static ?string $model = GeneralLedger::class;

    protected static bool $shouldRegisterNavigation = false;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookOpen;
    protected static string|\UnitEnum|null $navigationGroup = 'Keuangan';
    protected static ?string $modelLabel = 'Jurnal Umum (Ledger)';
    protected static ?string $pluralModelLabel = 'Jurnal Umum (Ledger)';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false;
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Infolists\Components\TextEntry::make('transaction_date')->label('Tanggal')->date('d M Y'),
                \Filament\Infolists\Components\TextEntry::make('chartOfAccount.account_code')->label('Kode Akun'),
                \Filament\Infolists\Components\TextEntry::make('chartOfAccount.name')->label('Nama Akun'),
                \Filament\Infolists\Components\TextEntry::make('description')->label('Keterangan'),
                \Filament\Infolists\Components\TextEntry::make('debit')->label('Debit (Rp)')->money('IDR'),
                \Filament\Infolists\Components\TextEntry::make('credit')->label('Kredit (Rp)')->money('IDR'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('transaction_date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('chartOfAccount.account_code')
                    ->label('Kode Akun')
                    ->sortable()
                    ->searchable(),
                \Filament\Tables\Columns\TextColumn::make('chartOfAccount.name')
                    ->label('Nama Akun')
                    ->sortable()
                    ->searchable(),
                \Filament\Tables\Columns\TextColumn::make('description')
                    ->label('Keterangan')
                    ->searchable()
                    ->limit(50),
                \Filament\Tables\Columns\TextColumn::make('debit')
                    ->label('Debit (Rp)')
                    ->money('IDR')
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('credit')
                    ->label('Kredit (Rp)')
                    ->money('IDR')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(function ($query) {
                $filter = \App\Livewire\GlobalHeader::getActiveDateRange();
                return $query->whereBetween('transaction_date', [
                    $filter['start'],
                    $filter['end'],
                ]);
            })
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageGeneralLedgers::route('/'),
        ];
    }
}
