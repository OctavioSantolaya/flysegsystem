<?php

namespace App\Filament\Operator\Resources\PassengerResource\Pages;

use App\Filament\Operator\Resources\PassengerResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManagePassengers extends ManageRecords
{
    protected static string $resource = PassengerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
