<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use Spatie\Permission\Models\Role;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama')
                    ->required(),
                TextInput::make('email')
                    ->label('Alamat Email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true),
                TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->required(fn (string $operation) => $operation === 'create')
                    ->minLength(8)
                    ->dehydrated(fn (?string $state) => filled($state))
                    ->helperText('Kosongkan jika tidak ingin mengubah password'),
                Select::make('roles')
                    ->label('Role / Hak Akses')
                    ->options(Role::pluck('name', 'name')->mapWithKeys(fn ($name) => [$name => str($name)->headline()->toString()]))
                    ->searchable()
                    ->required()
                    ->dehydrated(false)
                    ->afterStateHydrated(function (Select $component, $record) {
                        if ($record) {
                            $component->state($record->roles->pluck('name')->first());
                        }
                    }),
            ]);
    }
}
