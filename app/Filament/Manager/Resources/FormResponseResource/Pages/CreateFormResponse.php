<?php

namespace App\Filament\Manager\Resources\FormResponseResource\Pages;

use App\Filament\Manager\Resources\FormResponseResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateFormResponse extends CreateRecord
{
    protected static string $resource = FormResponseResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Aquí puedes agregar lógica personalizada antes de crear el registro
        return $data;
    }
}
