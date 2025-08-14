<?php

namespace App\Filament\Operator\Resources\FormResponseResource\Pages;

use App\Filament\Operator\Resources\FormResponseResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFormResponses extends ListRecords
{
    protected static string $resource = FormResponseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
