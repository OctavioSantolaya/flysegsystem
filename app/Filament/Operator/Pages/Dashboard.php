<?php

namespace App\Filament\Operator\Pages;

use App\Models\Contingency;
use Filament\Forms\Components\Select;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Actions\FilterAction;
use Filament\Pages\Dashboard\Concerns\HasFiltersAction;
use Illuminate\Support\Facades\Auth;

class Dashboard extends BaseDashboard
{
    use HasFiltersAction;
    
    public function getColumns(): int | string | array
    {
        return [
            'md' => 3,
            'xl' => 3,
        ];
    }

    protected function getHeaderActions(): array
    {
        // Obtener solo las contingencias de las bases del usuario autenticado
        $userContingencies = Contingency::whereHas('base.users', function ($query) {
            $query->where('user_id', Auth::id());
        })->get();

        $latestContingencyId = $userContingencies->sortByDesc('created_at')->first()?->id;

        return [
            FilterAction::make()
                ->form([
                    Select::make('contingency_id')
                        ->label('Seleccionar Contingencia')
                        ->placeholder('Todas las contingencias')
                        ->options(
                            $userContingencies
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
