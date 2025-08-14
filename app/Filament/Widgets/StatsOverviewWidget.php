<?php

namespace App\Filament\Widgets;

use App\Models\Contingency;
use App\Models\Passenger;
use App\Models\FormResponse;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

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
        
        // Si no hay filtro, usar la contingencia más reciente
        if (!$contingencyId) {
            $contingencyId = Contingency::latest('created_at')->value('id');
        }
        
        if ($contingencyId) {
            // Datos específicos de la contingencia seleccionada o más reciente
            $selectedContingency = Contingency::find($contingencyId);
            $totalPassengers = $selectedContingency ? $selectedContingency->passengers()->count() : 0;
            $totalFormResponses = FormResponse::where('contingency_id', $contingencyId)->count();
                
            return [
                Stat::make('Total de Pasajeros', $totalPassengers)
                    ->description($selectedContingency ? "Pasajeros de: {$selectedContingency->name}" : 'Contingencia no encontrada')
                    ->descriptionIcon('heroicon-o-users')
                    ->color('info'),
                    
                Stat::make('Respuestas Recibidas', $totalFormResponses)
                    ->description($totalPassengers > 0 ? 
                        round(($totalFormResponses / $totalPassengers) * 100, 1) . "% de respuestas" : 
                        'Sin respuestas')
                    ->descriptionIcon('heroicon-o-chat-bubble-left-right')
                    ->color('success'),
            ];
        } else {
            // No hay contingencias en el sistema
            return [
                Stat::make('No hay Contingencias', '0')
                    ->description('Aún no se han registrado contingencias')
                    ->descriptionIcon('heroicon-o-exclamation-triangle')
                    ->color('gray'),
                    
                Stat::make('Sin Datos', '—')
                    ->description('Registra una contingencia para ver estadísticas')
                    ->descriptionIcon('heroicon-o-information-circle')
                    ->color('gray'),
            ];
        }
    }
}
