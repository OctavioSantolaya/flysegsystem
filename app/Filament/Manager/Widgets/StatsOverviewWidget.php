<?php

namespace App\Filament\Manager\Widgets;

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
            
            // Contar solo pasajeros con respuestas
            $passengersWithResponses = $selectedContingency ? 
                $selectedContingency->passengers()->whereNotNull('form_response_id')->count() : 0;
            
            $totalFormResponses = FormResponse::where('contingency_id', $contingencyId)->count();
                
            return [
                Stat::make('Pasajeros con Respuesta', $passengersWithResponses)
                    ->description($selectedContingency ? "De {$totalPassengers} total en: {$selectedContingency->name}" : 'Contingencia no encontrada')
                    ->descriptionIcon('heroicon-o-users')
                    ->color('success'),
                    
                Stat::make('Respuestas Totales', $totalFormResponses)
                    ->description($totalPassengers > 0 ? 
                        round(($totalFormResponses / $totalPassengers) * 100, 1) . "% de cobertura" : 
                        'Sin respuestas')
                    ->descriptionIcon('heroicon-o-chat-bubble-left-right')
                    ->color('info'),
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
