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
