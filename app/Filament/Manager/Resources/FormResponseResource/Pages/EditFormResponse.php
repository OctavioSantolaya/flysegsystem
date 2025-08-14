<?php

namespace App\Filament\Manager\Resources\FormResponseResource\Pages;

use App\Filament\Manager\Resources\FormResponseResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFormResponse extends EditRecord
{
    protected static string $resource = FormResponseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }
}
