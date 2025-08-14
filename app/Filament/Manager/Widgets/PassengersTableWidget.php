<?php

namespace App\Filament\Manager\Widgets;

use App\Models\Passenger;
use App\Models\Contingency;
use App\Models\FormResponse;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Notifications\Notification;

class PassengersTableWidget extends BaseWidget
{
    use InteractsWithPageFilters;
    
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 'full';
    
    protected function getTableHeading(): string
    {
        $contingencyId = $this->filters['contingency_id'] ?? null;
        
        if (!$contingencyId) {
            $contingencyId = Contingency::latest('created_at')->value('id');
        }
        
        if ($contingencyId) {
            $contingency = Contingency::find($contingencyId);
            $passengersWithResponsesCount = $this->getTableQuery()->count();
            return $contingency ? "Pasajeros con Respuesta - {$contingency->name} ({$passengersWithResponsesCount} pasajeros)" : 'Pasajeros con Respuesta';
        }
        
        return 'Pasajeros con Respuesta';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('surname')
                    ->label('Apellido')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('pnr')
                    ->label('PNR')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->badge()
                    ->color('primary'),
                TextColumn::make('age')
                    ->label('Edad')
                    ->badge()
                    ->color(fn ($record) => $record->age < 18 ? 'warning' : 'success')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('email')
                    ->label('Correo Electrónico')
                    ->placeholder('No establecido')
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('phone')
                    ->label('Teléfono')
                    ->placeholder('No establecido')
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('document_number')
                    ->label('Documento')
                    ->placeholder('No establecido')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                BooleanColumn::make('formResponse')
                    ->label('Tiene Respuesta')
                    ->getStateUsing(fn ($record) => $record->form_response_id !== null)
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray'),
                TextColumn::make('created_at')
                    ->label('Registrado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups([
                Tables\Grouping\Group::make('form_response_id')
                    ->label('Respuesta')
                    ->getTitleFromRecordUsing(function ($record) {
                        return "Respuesta #{$record->form_response_id}";
                    })
                    ->getDescriptionFromRecordUsing(function ($record) {
                        $response = $record->formResponse;
                        if (!$response) return 'Sin respuesta';
                        
                        $details = [];
                        if ($response->needs_transport) $details[] = 'Transporte';
                        if ($response->needs_accommodation) $details[] = 'Alojamiento';
                        if ($response->has_medical_condition) $details[] = 'Condición Médica';
                        
                        return empty($details) ? 'Respuesta básica' : implode(', ', $details);
                    })
                    ->collapsible(),
            ])
            ->defaultGroup('form_response_id')
            ->actions([
                Tables\Actions\Action::make('editFormResponse')
                    ->label('Ver Respuesta')
                    ->icon('heroicon-o-document-text')
                    ->color('primary')
                    ->visible(fn ($record) => $record->form_response_id !== null)
                    ->url(fn ($record) => route('filament.manager.resources.form-responses.view', $record->form_response_id)),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped();
    }
    
    protected function getTableQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $contingencyId = $this->filters['contingency_id'] ?? null;
        
        // Si no hay filtro, usar la contingencia más reciente
        if (!$contingencyId) {
            $contingencyId = Contingency::latest('created_at')->value('id');
        }
        
        if ($contingencyId) {
            // Solo mostrar pasajeros que tienen respuesta cargada
            return Passenger::query()
                ->with('formResponse') // Cargar la relación formResponse
                ->where('contingency_id', $contingencyId)
                ->whereNotNull('form_response_id');
        }
        
        // Si no hay contingencias, retornar query vacío
        return Passenger::query()->whereRaw('1 = 0');
    }
}
