<?php

namespace App\Filament\Operator\Resources;

use App\Filament\Operator\Resources\FormResponseResource\Pages;
use App\Filament\Operator\Resources\FormResponseResource\RelationManagers;
use App\Models\FormResponse;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BooleanColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;

class FormResponseResource extends Resource
{
    protected static ?string $model = FormResponse::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Respuestas de Formulario';

    protected static ?string $modelLabel = 'Respuesta';

    protected static ?string $pluralModelLabel = 'Respuestas';

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Wizard\Step::make('Transporte')
                        ->schema([
                            Toggle::make('needs_transport')
                                ->label('Necesita Transporte')
                                ->default(false)
                                ->live(),
                            TextInput::make('transport_address')
                                ->label('Dirección para Transporte')
                                ->placeholder('Ingrese la dirección de transporte')
                                ->maxLength(255)
                                ->disabled(fn($get) => !$get('needs_transport'))
                                ->required(fn($get) => $get('needs_transport')),
                            TextInput::make('luggage_count')
                                ->label('Cantidad de Equipaje')
                                ->numeric()
                                ->default(1)
                                ->minValue(0)
                                ->disabled(fn($get) => !$get('needs_transport'))
                                ->required(fn($get) => $get('needs_transport')),
                        ]),
                    Wizard\Step::make('Alojamiento')
                        ->schema([
                            Toggle::make('needs_accommodation')
                                ->label('Necesita Alojamiento')
                                ->default(false)
                                ->live(),
                            TextInput::make('children_count')
                                ->label('Cantidad de Niños')
                                ->numeric()
                                ->default(0)
                                ->minValue(0)
                                ->maxValue(fn($record) => optional($record)->passengers?->count() > 0 ? $record->passengers->count() - 1 : 0)
                                ->disabled(fn($get) => !$get('needs_accommodation'))
                                ->required(fn($get) => $get('needs_accommodation'))
                                ->live(),
                            Repeater::make('children_ages')
                                ->label('Edades de los Niños')
                                ->schema([
                                    TextInput::make('age')
                                        ->label('Edad')
                                        ->numeric()
                                        ->minValue(1)
                                        ->maxValue(17)
                                        ->required(),
                                ])
                                ->addActionLabel('Agregar Edad')
                                ->itemLabel(fn(array $state): ?string => $state['age'] ? "Niño de {$state['age']} años" : null)
                                ->minItems(fn($get) => $get('children_count'))
                                ->maxItems(fn($get) => $get('children_count'))
                                ->visible(fn($get) => $get('needs_accommodation') && $get('children_count') > 0)
                                ->required(fn($get) => $get('needs_accommodation') && $get('children_count') > 0),
                        ]),
                    Wizard\Step::make('Alimentación')
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
                        ]),
                    Wizard\Step::make('Condición Médica')
                        ->schema([
                            Toggle::make('has_medical_condition')
                                ->label('Tiene Condición Médica')
                                ->default(false)
                                ->live(),
                            Textarea::make('medical_condition_details')
                                ->label('Detalles de Condición Médica')
                                ->rows(3)
                                ->visible(fn($get) => $get('has_medical_condition'))
                                ->required(fn($get) => $get('has_medical_condition')),
                        ]),
                    Wizard\Step::make('Reprogramación de Vuelo')
                        ->schema([
                            Toggle::make('has_flight_reprogramming')
                                ->label('Tiene Reprogramación de Vuelo')
                                ->default(false)
                                ->live(),
                            TextInput::make('reprogrammed_flight_number')
                                ->label('Número de Vuelo Reprogramado')
                                ->placeholder('Ej: LA123')
                                ->maxLength(10)
                                ->visible(fn($get) => $get('has_flight_reprogramming'))
                                ->required(fn($get) => $get('has_flight_reprogramming')),
                            DatePicker::make('reprogrammed_flight_date')
                                ->label('Fecha del Vuelo Reprogramado')
                                ->visible(fn($get) => $get('has_flight_reprogramming'))
                                ->required(fn($get) => $get('has_flight_reprogramming')),
                        ]),
                ])->columnSpanFull()
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Tabs::make('Tabs')
                    ->tabs([
                        Tabs\Tab::make('Transporte')
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
                            ]),
                        Tabs\Tab::make('Alojamiento')
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
                        Tabs\Tab::make('Condición Médica')
                            ->schema([
                                IconEntry::make('has_medical_condition')
                                    ->label('Tiene Condición Médica')
                                    ->boolean(),
                                TextEntry::make('medical_condition_details')
                                    ->label('Detalles')
                                    ->visible(fn($record) => $record->has_medical_condition),
                            ]),
                        Tabs\Tab::make('Reprogramación')
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
                    ->placeholder('No especificada'),
                TextColumn::make('luggage_count')
                    ->label('Equipajes')
                    ->badge()
                    ->color('primary'),
                BooleanColumn::make('needs_accommodation')
                    ->label('Alojamiento'),
                TextColumn::make('children_count')
                    ->label('Niños')
                    ->badge()
                    ->color('warning'),
                TextColumn::make('children_ages')
                    ->label('Edades')
                    ->formatStateUsing(fn($state) => $state ? implode(', ', $state) . ' años' : 'N/A')
                    ->toggleable(isToggledHiddenByDefault: true),
                BooleanColumn::make('has_medical_condition')
                    ->label('Condición Médica'),
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
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['contingency.base', 'passengers'])
            ->whereHas('contingency.base.users', function ($query) {
                $query->where('user_id', Auth::id());
            });
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
            'create' => Pages\CreateFormResponse::route('/create'),
            'view' => Pages\ViewFormResponse::route('/{record}'),
            'edit' => Pages\EditFormResponse::route('/{record}/edit'),
        ];
    }
}
