<?php

namespace App\Filament\Manager\Pages;

use App\Models\Contingency;
use Filament\Forms\Components\Select;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Actions\FilterAction;
use Filament\Pages\Dashboard\Concerns\HasFiltersAction;

class Dashboard extends BaseDashboard
{
    use HasFiltersAction;
    
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static string $routePath = '/';
    protected static ?string $title = 'Escritorio';
    protected static ?string $navigationLabel = 'Escritorio';
    
    public function getColumns(): int | string | array
    {
        return [
            'md' => 3,
            'xl' => 3,
        ];
    }

    protected function getHeaderActions(): array
    {
        $latestContingencyId = Contingency::latest('created_at')->value('id');

        return [
            FilterAction::make()
                ->form([
                    Select::make('contingency_id')
                        ->label('Seleccionar Contingencia')
                        ->placeholder('Todas las contingencias')
                        ->options(
                            Contingency::all()
                                ->pluck('name', 'id')
                                ->map(fn ($name, $id) => "{$name}")
                                ->toArray()
                        )
                        ->searchable()
                        ->preload()
                        ->default($latestContingencyId),
                ]),
        ];
    }
}