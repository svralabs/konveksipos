<?php

namespace App\Filament\Resources\CashRegisters;

use App\Filament\Resources\CashRegisters\Pages\ManageCashRegisters;
use App\Models\CashRegister;
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

class CashRegisterResource extends Resource
{
    protected static ?string $model = CashRegister::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArchiveBox;
    protected static string|\UnitEnum|null $navigationGroup = 'Transaksi';
    protected static ?string $modelLabel = 'Shift Kasir';
    protected static ?string $pluralModelLabel = 'Shift Kasir';

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
                \Filament\Infolists\Components\Section::make('Informasi Shift')
                    ->schema([
                        \Filament\Infolists\Components\TextEntry::make('user.name')->label('Kasir'),
                        \Filament\Infolists\Components\TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->color(fn (string $state): string => $state === 'open' ? 'success' : 'gray')
                            ->formatStateUsing(fn (string $state): string => $state === 'open' ? 'Buka' : 'Tutup'),
                        \Filament\Infolists\Components\TextEntry::make('opening_amount')
                            ->label('Modal Awal')
                            ->money('IDR'),
                        \Filament\Infolists\Components\TextEntry::make('closing_amount')
                            ->label('Setoran Akhir')
                            ->money('IDR'),
                        \Filament\Infolists\Components\TextEntry::make('opened_at')
                            ->label('Waktu Buka')
                            ->dateTime('d M Y H:i:s'),
                        \Filament\Infolists\Components\TextEntry::make('closed_at')
                            ->label('Waktu Tutup')
                            ->dateTime('d M Y H:i:s'),
                        \Filament\Infolists\Components\TextEntry::make('notes')
                            ->label('Catatan')
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('user.name')
                    ->label('Kasir')
                    ->sortable()
                    ->searchable(),
                \Filament\Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'open' ? 'success' : 'gray')
                    ->formatStateUsing(fn (string $state): string => $state === 'open' ? 'Buka' : 'Tutup')
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('opening_amount')
                    ->label('Modal Awal')
                    ->money('IDR')
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('closing_amount')
                    ->label('Setoran Akhir')
                    ->money('IDR')
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('opened_at')
                    ->label('Waktu Buka')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('closed_at')
                    ->label('Waktu Tutup')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('opened_at', 'desc')
            ->modifyQueryUsing(function ($query) {
                $filter = \App\Livewire\GlobalHeader::getActiveDateRange();
                return $query->whereBetween('opened_at', [
                    $filter['start'] . ' 00:00:00',
                    $filter['end']   . ' 23:59:59',
                ]);
            })
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('status')
                    ->label('Status Shift')
                    ->options([
                        'open' => 'Buka',
                        'closed' => 'Tutup',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageCashRegisters::route('/'),
        ];
    }
}
