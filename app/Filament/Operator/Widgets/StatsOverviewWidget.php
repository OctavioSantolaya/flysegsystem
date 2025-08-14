<?php

namespace App\Filament\Operator\Widgets;

use App\Models\Contingency;
use App\Models\Passenger;
use App\Models\FormResponse;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Facades\Auth;

class StatsOverviewWidget extends BaseWidget
{
    use InteractsWithPageFilters;
    
    protected static ?int $sort = 1;
    protected int | string | array $columnSpan = 'full';
    
    protected function getColumns(): int
    {
        return 2;
    }
    
    protected function getStats(): array
    {
        $contingencyId = $this->filters['contingency_id'] ?? null;
        
        // Si no hay filtro, usar la contingencia más reciente de las bases del usuario
        if (!$contingencyId) {
            $contingencyId = Contingency::whereHas('base.users', function ($query) {
                $query->where('user_id', Auth::id());
            })->latest('created_at')->value('id');
        }
        
        if ($contingencyId) {
            // Verificar que la contingencia pertenece a una base del usuario
            $selectedContingency = Contingency::whereHas('base.users', function ($query) {
                $query->where('user_id', Auth::id());
            })->find($contingencyId);
            
            if ($selectedContingency) {
                $totalPassengers = $selectedContingency->passengers()->count();
                $totalFormResponses = FormResponse::where('contingency_id', $contingencyId)->count();
                    
                return [
                    Stat::make('Total de Pasajeros', $totalPassengers)
                        ->description("Pasajeros de: {$selectedContingency->name}")
                        ->descriptionIcon('heroicon-o-users')
                        ->color('info'),
                        
                    Stat::make('Respuestas Recibidas', $totalFormResponses)
                        ->description($totalPassengers > 0 ? 
                            round(($totalFormResponses / $totalPassengers) * 100, 1) . "% de respuestas" : 
                            'Sin respuestas')
                        ->descriptionIcon('heroicon-o-chat-bubble-left-right')
                        ->color('success'),
                ];
            }
        }
        
        // No hay contingencias accesibles para el usuario
        return [
            Stat::make('No hay Contingencias', '0')
                ->description('Aún no tienes contingencias asignadas')
                ->descriptionIcon('heroicon-o-exclamation-triangle')
                ->color('gray'),
                
            Stat::make('Sin Datos', '—')
                ->description('Contacta al administrador para asignar bases')
                ->descriptionIcon('heroicon-o-information-circle')
                ->color('gray'),
        ];
    }
}
