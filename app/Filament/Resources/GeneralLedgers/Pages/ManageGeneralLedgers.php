<?php

namespace App\Filament\Resources\GeneralLedgers\Pages;

use App\Filament\Resources\GeneralLedgers\GeneralLedgerResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageGeneralLedgers extends ManageRecords
{
    protected static string $resource = GeneralLedgerResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
