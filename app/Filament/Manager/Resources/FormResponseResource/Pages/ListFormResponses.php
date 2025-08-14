<?php

namespace App\Filament\Manager\Resources\FormResponseResource\Pages;

use App\Filament\Manager\Resources\FormResponseResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFormResponses extends ListRecords
{
    protected static string $resource = FormResponseResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
