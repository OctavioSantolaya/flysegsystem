<?php

namespace App\Filament\Manager\Resources;

use App\Filament\Manager\Resources\FormResponseResource\Pages;
use App\Filament\Manager\Resources\FormResponseResource\RelationManagers;
use App\Models\FormResponse;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BooleanColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\Section as InfolistSection;

class FormResponseResource extends Resource
{
    protected static ?string $model = FormResponse::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Respuestas';

    protected static ?string $modelLabel = 'Respuesta';

    protected static ?string $pluralModelLabel = 'Respuestas';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Asignaciones del Gestor')
                    ->description('Complete la información de asignación de transporte y alojamiento')
                    ->schema([
                        Textarea::make('assigned_transport_info')
                            ->label('Asignación de Transporte')
                            ->placeholder('Ingrese los detalles del transporte asignado (empresa, horario, número de contacto, etc.)...')
                            ->rows(4)
                            ->helperText('Solo complete si el pasajero necesita transporte'),
                        Textarea::make('assigned_accommodation_info')
                            ->label('Asignación de Alojamiento')
                            ->placeholder('Ingrese los detalles del alojamiento asignado (hotel, dirección, número de contacto, etc.)...')
                            ->rows(4)
                            ->helperText('Solo complete si el pasajero necesita alojamiento'),
                    ])->columns(1),
                Section::make('Información de Alimentación')
                    ->schema([
                        TextInput::make('food_service_provider')
                            ->label('Empresa de Catering')
                            ->placeholder('Ingrese el nombre de la empresa')
                            ->nullable(),
                        Select::make('food_service_type')
                            ->label('Tipo de Servicio')
                            ->options([
                                'Desayuno' => 'Desayuno',
                                'Almuerzo' => 'Almuerzo',
                                'Merienda' => 'Merienda',
                                'Cena' => 'Cena',
                                'Snack' => 'Snack',
                            ])
                            ->nullable(),
                    ])->columns(2),
            ]);
    }



    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Tabs::make('Tabs')
                    ->tabs([
                        Tabs\Tab::make('Detalles')
                            ->schema([
                                InfolistSection::make('Transporte')
                                    ->schema([
                                        IconEntry::make('needs_transport')
                                            ->label('Necesita Transporte')
                                            ->boolean(),
                                        TextEntry::make('transport_address')
                                            ->label('Dirección')
                                            ->placeholder('No especificada')
                                            ->visible(fn($record) => $record->needs_transport),
                                        TextEntry::make('luggage_count')
                                            ->label('Equipaje')
                                            ->visible(fn($record) => $record->needs_transport),
                                    ])->columns(3),
                                InfolistSection::make('Alojamiento')
                                    ->schema([
                                        IconEntry::make('needs_accommodation')
                                            ->label('Necesita Alojamiento')
                                            ->boolean(),
                                        TextEntry::make('children_count')
                                            ->label('Cantidad de Niños')
                                            ->visible(fn($record) => $record->needs_accommodation),
                                        TextEntry::make('children_ages')
                                            ->label('Edades de Niños')
                                            ->listWithLineBreaks()
                                            ->bulleted()
                                            ->visible(fn($record) => $record->needs_accommodation && $record->children_count > 0),
                                    ])->columns(3),
                                InfolistSection::make('Condición Médica')
                                    ->schema([
                                        IconEntry::make('has_medical_condition')
                                            ->label('Tiene Condición Médica')
                                            ->boolean(),
                                        TextEntry::make('medical_condition_details')
                                            ->label('Detalles')
                                            ->visible(fn($record) => $record->has_medical_condition),
                                    ])->columns(2),
                                InfolistSection::make('Reprogramación')
                                    ->schema([
                                        IconEntry::make('has_flight_reprogramming')
                                            ->label('Tiene Reprogramación')
                                            ->boolean(),
                                        TextEntry::make('reprogrammed_flight_number')
                                            ->label('Nuevo Vuelo')
                                            ->visible(fn($record) => $record->has_flight_reprogramming),
                                        TextEntry::make('reprogrammed_flight_date')
                                            ->label('Nueva Fecha')
                                            ->date('d/m/Y')
                                            ->visible(fn($record) => $record->has_flight_reprogramming),
                                    ])->columns(3),
                            ]),
                        Tabs\Tab::make('Asignaciones')
                            ->schema([
                                TextEntry::make('assigned_transport_info')
                                    ->label('Asignación de Transporte')
                                    ->placeholder('Sin asignación'),
                                TextEntry::make('assigned_accommodation_info')
                                    ->label('Asignación de Alojamiento')
                                    ->placeholder('Sin asignación'),
                            ]),
                        Tabs\Tab::make('Alimentación')
                            ->schema([
                                TextEntry::make('food_service_provider')
                                    ->label('Empresa de Catering')
                                    ->placeholder('No asignada'),
                                TextEntry::make('food_service_type')
                                    ->label('Tipo de Servicio')
                                    ->placeholder('No especificado'),
                            ]),
                    ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('contingency.contingency_id')
                    ->label('Contingencia')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),
                BooleanColumn::make('needs_transport')
                    ->label('Transporte'),
                TextColumn::make('transport_address')
                    ->label('Dirección')
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->placeholder('No especificada'),
                TextColumn::make('luggage_count')
                    ->label('Equipajes')
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->color('primary'),
                BooleanColumn::make('needs_accommodation')
                    ->label('Alojamiento'),
                BooleanColumn::make('has_medical_condition')
                    ->label('Condición Médica'),
                TextColumn::make('children_count')
                    ->label('Niños')
                    ->badge()
                    ->color('warning'),
                TextColumn::make('children_ages')
                    ->label('Edades')
                    ->formatStateUsing(fn($state) => $state ? implode(', ', $state) . ' años' : 'N/A')
                    ->toggleable(isToggledHiddenByDefault: true),
                BooleanColumn::make('has_flight_reprogramming')
                    ->label('Reprogramación')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('reprogrammed_flight_number')
                    ->label('Vuelo Nuevo')
                    ->placeholder('N/A')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('reprogrammed_flight_date')
                    ->label('Fecha Nueva')
                    ->date('d/m/Y')
                    ->placeholder('N/A')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('passengers_count')
                    ->label('Pasajeros')
                    ->counts('passengers')
                    ->badge()
                    ->color('success'),
                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\Filter::make('needs_transport')
                    ->label('Necesita Transporte')
                    ->query(fn(Builder $query): Builder => $query->where('needs_transport', true)),
                Tables\Filters\Filter::make('needs_accommodation')
                    ->label('Necesita Alojamiento')
                    ->query(fn(Builder $query): Builder => $query->where('needs_accommodation', true)),
                Tables\Filters\Filter::make('has_medical_condition')
                    ->label('Condición Médica')
                    ->query(fn(Builder $query): Builder => $query->where('has_medical_condition', true)),
                Tables\Filters\Filter::make('has_flight_reprogramming')
                    ->label('Tiene Reprogramación')
                    ->query(fn(Builder $query): Builder => $query->where('has_flight_reprogramming', true)),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['contingency', 'passengers']);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\PassengersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFormResponses::route('/'),
            'view' => Pages\ViewFormResponse::route('/{record}'),
            'edit' => Pages\EditFormResponse::route('/{record}/edit'),
        ];
    }
}
