<?php

namespace App\Filament\Resources\ActivityLogs;

use App\Filament\Resources\ActivityLogs\Pages\ManageActivityLogs;
use Spatie\Activitylog\Models\Activity;
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

class ActivityLogResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;
    protected static string|\UnitEnum|null $navigationGroup = 'Sistem';
    protected static ?string $modelLabel = 'Riwayat Aktivitas';
    protected static ?string $pluralModelLabel = 'Riwayat Aktivitas';
    
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
                \Filament\Schemas\Components\Section::make('Informasi Aktivitas')
                    ->schema([
                        \Filament\Infolists\Components\TextEntry::make('created_at')
                            ->label('Waktu')
                            ->dateTime('d M Y H:i:s'),
                        \Filament\Infolists\Components\TextEntry::make('causer.name')
                            ->label('User / Pelaku')
                            ->placeholder('Sistem / Otomatis'),
                        \Filament\Infolists\Components\TextEntry::make('log_name')
                            ->label('Modul')
                            ->badge(),
                        \Filament\Infolists\Components\TextEntry::make('description')
                            ->label('Aktivitas / Deskripsi'),
                        \Filament\Infolists\Components\TextEntry::make('subject_type')
                            ->label('Tipe Target')
                            ->formatStateUsing(fn (?string $state): string => $state ? class_basename($state) : '-')
                            ->placeholder('-'),
                        \Filament\Infolists\Components\TextEntry::make('subject_id')
                            ->label('ID Target')
                            ->placeholder('-'),
                    ])->columns(2),

                \Filament\Schemas\Components\Section::make('Detail Data & Perubahan')
                    ->schema([
                        \Filament\Infolists\Components\TextEntry::make('properties')
                            ->label('Data Properties / Payload')
                            ->formatStateUsing(function ($state): string {
                                if (empty($state) || (is_array($state) && count($state) === 0)) {
                                    return '-';
                                }
                                $json = json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                return "<pre style='background: #18181b; color: #34d399; padding: 12px; border-radius: 6px; overflow-x: auto; font-family: monospace; font-size: 12px; font-weight: 500;'>{$json}</pre>";
                            })
                            ->html()
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime('d M Y H:i:s')
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('causer.name')
                    ->label('User')
                    ->sortable()
                    ->searchable(),
                \Filament\Tables\Columns\TextColumn::make('log_name')
                    ->label('Modul')
                    ->badge()
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('description')
                    ->label('Aktivitas')
                    ->searchable(),
                \Filament\Tables\Columns\TextColumn::make('subject_type')
                    ->label('Target')
                    ->formatStateUsing(fn(string $state): string => class_basename($state))
                    ->sortable(),
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
                \Filament\Tables\Filters\SelectFilter::make('log_name')
                    ->label('Modul')
                    ->options([
                        'orders' => 'Penjualan',
                        'expenses' => 'Pengeluaran',
                        'stock' => 'Stok',
                        'auth' => 'Otentikasi',
                        'default' => 'Sistem / Umum',
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
            'index' => ManageActivityLogs::route('/'),
        ];
    }
}
