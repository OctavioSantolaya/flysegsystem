<?php

namespace App\Filament\Manager\Resources\FormResponseResource\Pages;

use App\Filament\Manager\Resources\FormResponseResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\Tabs\Tab;

class ViewFormResponse extends ViewRecord
{
    protected static string $resource = FormResponseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Cargar las relaciones necesarias
        $this->record->load(['passengers', 'contingency']);
        return $data;
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Información General')
                    ->schema([
                        TextEntry::make('contingency.contingency_id')
                            ->label('ID de Contingencia')
                            ->badge()
                            ->color('info'),
                        TextEntry::make('contingency.flight_number')
                            ->label('Número de Vuelo')
                            ->badge()
                            ->color('primary'),
                        TextEntry::make('created_at')
                            ->label('Fecha de Creación')
                            ->dateTime('d/m/Y H:i'),
                        TextEntry::make('updated_at')
                            ->label('Fecha de Modificación')
                            ->dateTime('d/m/Y H:i'),
                    ])->columnSpan(1)->columns(2),

                Tabs::make('Tabs')
                    ->tabs([
                        Tab::make('Transporte')
                            ->schema([
                                IconEntry::make('needs_transport')
                                    ->label('Necesita Transporte')
                                    ->boolean()
                                    ->trueIcon('heroicon-o-check-circle')
                                    ->falseIcon('heroicon-o-x-circle')
                                    ->trueColor('success')
                                    ->falseColor('gray'),
                                TextEntry::make('transport_address')
                                    ->label('Dirección para Transporte')
                                    ->placeholder('No especificada')
                                    ->columnSpan(1)
                                    ->visible(fn($record) => $record->needs_transport),
                                TextEntry::make('luggage_count')
                                    ->label('Cantidad de Equipaje')
                                    ->badge()
                                    ->color('primary')
                                    ->suffix(' piezas')
                                    ->visible(fn($record) => $record->needs_transport),
                                Section::make('Asignación de Transporte')
                                    ->description('Detalles de la Asignación de Transporte')
                                    ->schema([
                                        TextEntry::make('assigned_transport_info')
                                            ->label(false)
                                            ->placeholder('No asignada')
                                            ->markdown()
                                            ->columnSpanFull(),
                                    ])
                                    ->collapsed()
                            ])->columns(3),
                        Tab::make('Alojamiento')
                            ->schema([
                                IconEntry::make('needs_accommodation')
                                    ->label('Necesita Alojamiento')
                                    ->boolean()
                                    ->trueIcon('heroicon-o-check-circle')
                                    ->falseIcon('heroicon-o-x-circle')
                                    ->trueColor('success')
                                    ->falseColor('gray'),
                                TextEntry::make('children_count')
                                    ->label('Cantidad de Niños')
                                    ->badge()
                                    ->color('warning')
                                    ->suffix(' niños')
                                    ->visible(fn($record) => $record->needs_accommodation),
                                Section::make('Asignación de Alojamiento')
                                    ->description('Detalles de la Asignación de Alojamiento')
                                    ->schema([
                                        TextEntry::make('assigned_accommodation_info')
                                            ->label(false)
                                            ->placeholder('No asignada')
                                            ->markdown()
                                            ->columnSpan(1),
                                    ])
                                    ->collapsed()
                            ])->columns(3),
                        Tab::make('Alimentación')
                            ->schema([
                                TextEntry::make('food_service_provider')
                                    ->label('Empresa de Catering')
                                    ->placeholder('No asignada'),
                                TextEntry::make('food_service_type')
                                    ->label('Tipo de Servicio')
                                    ->placeholder('No especificado'),
                            ]),
                        Tab::make('Condición Médica')
                            ->schema([
                                IconEntry::make('has_medical_condition')
                                    ->label('Tiene Condición Médica')
                                    ->boolean()
                                    ->trueIcon('heroicon-o-check-circle')
                                    ->falseIcon('heroicon-o-x-circle')
                                    ->trueColor('success')
                                    ->falseColor('gray'),
                                TextEntry::make('medical_condition_details')
                                    ->label('Detalles de Condición Médica')
                                    ->placeholder('No especificada')
                                    ->markdown()
                                    ->visible(fn($record) => $record->has_medical_condition && $record->medical_condition_details)
                                    ->columnSpanFull(),
                            ])->columns(3),
                        Tab::make('Reprogramación de Vuelo')
                            ->schema([
                                IconEntry::make('has_flight_reprogramming')
                                    ->label('Tiene Reprogramación de Vuelo')
                                    ->boolean()
                                    ->trueIcon('heroicon-o-check-circle')
                                    ->falseIcon('heroicon-o-x-circle')
                                    ->trueColor('success')
                                    ->falseColor('gray'),
                                TextEntry::make('reprogrammed_flight_number')
                                    ->label('Número de Vuelo Reprogramado')
                                    ->badge()
                                    ->color('info')
                                    ->placeholder('No especificado')
                                    ->visible(fn($record) => $record->has_flight_reprogramming),
                                TextEntry::make('reprogrammed_flight_date')
                                    ->label('Fecha de Vuelo Reprogramado')
                                    ->date('d/m/Y')
                                    ->badge()
                                    ->color('warning')
                                    ->placeholder('No especificada')
                                    ->visible(fn($record) => $record->has_flight_reprogramming),
                                Section::make('Información de Reprogramación')
                                    ->description('Detalles adicionales sobre la reprogramación del vuelo')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                TextEntry::make('contingency.flight_number')
                                                    ->label('Vuelo Original')
                                                    ->badge()
                                                    ->color('gray'),
                                                TextEntry::make('reprogrammed_flight_number')
                                                    ->label('Vuelo Reprogramado')
                                                    ->badge()
                                                    ->color('success')
                                                    ->placeholder('No especificado'),
                                            ])
                                            ->visible(fn($record) => $record->has_flight_reprogramming),
                                        TextEntry::make('reprogrammed_flight_date')
                                            ->label('Nueva Fecha de Vuelo')
                                            ->dateTime('d/m/Y H:i')
                                            ->placeholder('No especificada')
                                            ->visible(fn($record) => $record->has_flight_reprogramming),
                                    ])
                                    ->collapsed()
                                    ->visible(fn($record) => $record->has_flight_reprogramming)
                            ])->columns(3),
                    ])->columnSpan(2),
            ])->columns(3);
    }
}
