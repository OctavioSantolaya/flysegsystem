<?php

namespace App\Filament\Operator\Resources\ContingencyResource\Pages;

use App\Filament\Operator\Resources\ContingencyResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ListContingencies extends ListRecords
{
    protected static string $resource = ContingencyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'activas' => Tab::make('Activas')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('finished', false))
                ->badge(fn () => $this->getModel()::where('finished', false)->whereHas('base.users', function ($query) {
                    $query->where('user_id', Auth::id());
                })->count()),
            
            'finalizadas' => Tab::make('Finalizadas')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('finished', true))
                ->badge(fn () => $this->getModel()::where('finished', true)->whereHas('base.users', function ($query) {
                    $query->where('user_id', Auth::id());
                })->count()),
        ];
    }

    public function getDefaultActiveTab(): string | int | null
    {
        return 'activas';
    }
}
