<?php

namespace App\Filament\Operator\Widgets;

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
use Illuminate\Support\Facades\Auth;

class PassengersTableWidget extends BaseWidget
{
    use InteractsWithPageFilters;
    
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 'full';
    
    protected function getTableHeading(): string
    {
        $contingencyId = $this->filters['contingency_id'] ?? null;
        
        if (!$contingencyId) {
            $contingencyId = Contingency::whereHas('base.users', function ($query) {
                $query->where('user_id', Auth::id());
            })->latest('created_at')->value('id');
        }
        
        if ($contingencyId) {
            $contingency = Contingency::whereHas('base.users', function ($query) {
                $query->where('user_id', Auth::id());
            })->find($contingencyId);
            return $contingency ? "Pasajeros - {$contingency->name}" : 'Pasajeros';
        }
        
        return 'Pasajeros';
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
            ->actions([
                Tables\Actions\Action::make('createFormResponse')
                    ->label('Crear Respuesta')
                    ->icon('heroicon-o-document-plus')
                    ->color('success')
                    ->visible(fn ($record) => $record->form_response_id === null)
                    ->action(function ($record) {
                        $formResponse = FormResponse::create([
                            'contingency_id' => $record->contingency_id,
                            'needs_transport' => false,
                            'luggage_count' => 1,
                            'needs_accommodation' => false,
                            'children_count' => 0,
                            'has_medical_condition' => false,
                        ]);
                        
                        $record->update(['form_response_id' => $formResponse->id]);
                        
                        Notification::make()
                            ->title('Respuesta creada exitosamente')
                            ->body("Se creó una nueva respuesta para {$record->name} {$record->surname}")
                            ->success()
                            ->send();
                            
                        return redirect()->route('filament.operator.resources.form-responses.edit', $formResponse);
                    }),
                Tables\Actions\Action::make('editFormResponse')
                    ->label('Ver Respuesta')
                    ->icon('heroicon-o-document-text')
                    ->color('primary')
                    ->visible(fn ($record) => $record->form_response_id !== null)
                    ->url(fn ($record) => route('filament.operator.resources.form-responses.view', $record->form_response_id)),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped();
    }
    
    protected function getTableQuery(): \Illuminate\Database\Eloquent\Builder
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
            $contingencyExists = Contingency::whereHas('base.users', function ($query) {
                $query->where('user_id', Auth::id());
            })->where('id', $contingencyId)->exists();
            
            if ($contingencyExists) {
                return Passenger::query()->where('contingency_id', $contingencyId);
            }
        }
        
        // Si no hay contingencias accesibles, retornar query vacío
        return Passenger::query()->whereRaw('1 = 0');
    }
}
