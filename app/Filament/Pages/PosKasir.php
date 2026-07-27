<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class PosKasir extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shopping-cart';
    protected static string|\UnitEnum|null $navigationGroup = 'Transaksi';
    protected static ?string $title = 'Kasir / POS';
    protected static ?string $navigationLabel = 'Kasir / POS';

    protected string $view = 'filament.pages.pos-kasir';
}
