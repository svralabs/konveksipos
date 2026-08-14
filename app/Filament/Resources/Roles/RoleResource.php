<?php

namespace App\Filament\Resources\Roles;

use BezhanSalleh\FilamentShield\Resources\Roles\RoleResource as ShieldRoleResource;
use Filament\Support\Icons\Heroicon;
use BackedEnum;

class RoleResource extends ShieldRoleResource
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedLockClosed;
    protected static string|\UnitEnum|null $navigationGroup = 'Pengaturan';
    protected static ?string $modelLabel = 'Role / Hak Akses';
    protected static ?string $pluralModelLabel = 'Role & Hak Akses';
    protected static ?string $navigationLabel = 'Role & Hak Akses';
    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return 'Pengaturan';
    }

    public static function getNavigationLabel(): string
    {
        return 'Role & Hak Akses';
    }

    public static function getModelLabel(): string
    {
        return 'Role / Hak Akses';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Role & Hak Akses';
    }
}
