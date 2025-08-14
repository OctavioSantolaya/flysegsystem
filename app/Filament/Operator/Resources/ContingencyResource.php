<?php

namespace App\Filament\Operator\Resources;

use App\Exports\ContingencyExport;
use App\Filament\Operator\Resources\ContingencyResource\Pages;
use App\Filament\Operator\Resources\ContingencyResource\RelationManagers;
use App\Models\Contingency;
use App\Models\Base;
use App\Models\Airline;
use App\Models\User;
use Faker\Provider\ar_EG\Text;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\Section as InfolistSection;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class ContingencyResource extends Resource
{
    protected static ?string $model = Contingency::class;

    protected static ?string $navigationIcon = 'heroicon-o-exclamation-triangle';

    protected static ?string $modelLabel = 'Contingencia';

    protected static ?string $pluralModelLabel = 'Contingencias';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Información de la Contingencia')
                    ->description('Detalles principales de la contingencia')
                    ->schema([
                        TextInput::make('contingency_id')
                            ->label('Identificador')
                            ->required()
                            ->columnSpan(3)
                            ->maxLength(255),
                        TextInput::make('flight_number')
                            ->label('Número de Vuelo')
                            ->required()
                            ->columnSpan(3)
                            ->maxLength(255),
                        Select::make('contingency_type')
                            ->label('Tipo de Contingencia')
                            ->columnSpan(2)
                            ->required()
                            ->options(Contingency::getContingencyTypes()),
                        TextInput::make('scale')
                            ->label('Ruta')
                            ->placeholder('Ej: Madrid-Buenos Aires')
                            ->columnSpan(2)
                            ->maxLength(255),
                        DateTimePicker::make('date')
                            ->label('Fecha y Hora')
                            ->format('Y-m-d')
                            ->columnSpan(2)
                            ->required()
                            ->default(now())
                            ->native(false),
                        Toggle::make('finished')
                            ->label('Finalizada')
                            ->columnSpan(2)
                            ->default(false)
                            ->inline(false)
                            ->visibleOn('edit')
                            ->disabledOn('create'),
                    ])->columns(6)->columnSpan(4),

                Section::make('Ubicación y Aerolínea')
                    ->description('Selecciona la base y aerolínea relacionada')
                    ->schema([
                        Select::make('base_id')
                            ->label('Base')
                            ->required()
                            ->options(function () {
                                // Solo bases asignadas al usuario actual
                                $user = User::find(Auth::id());
                                return $user ? $user->bases()->pluck('bases.name', 'bases.id') : [];
                            })
                            ->default(function () {
                                // Auto-seleccionar si solo hay una base disponible
                                $user = User::find(Auth::id());
                                if ($user) {
                                    $userBases = $user->bases()->pluck('bases.id');
                                    return $userBases->count() === 1 ? $userBases->first() : null;
                                }
                                return null;
                            })
                            ->reactive()
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                $set('airline_id', null);
                                
                                // Auto-seleccionar aerolínea si solo hay una disponible
                                if ($state) {
                                    $base = Base::find($state);
                                    if ($base) {
                                        $airlines = $base->airlines()->pluck('airlines.id');
                                        if ($airlines->count() === 1) {
                                            $set('airline_id', $airlines->first());
                                        }
                                    }
                                }
                            }),
                        Select::make('airline_id')
                            ->label('Aerolínea')
                            ->required()
                            ->options(function (Forms\Get $get) {
                                $baseId = $get('base_id');
                                if (!$baseId) {
                                    return [];
                                }
                                // Solo aerolíneas asociadas a la base seleccionada
                                $base = Base::find($baseId);
                                return $base ? $base->airlines()->pluck('airlines.name', 'airlines.id') : [];
                            })
                            ->disabled(fn(Forms\Get $get): bool => !$get('base_id')),
                    ])->columnSpan(2),

                Hidden::make('user_id')
                    ->default(Auth::id()),
            ])->columns(6);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                InfolistSection::make('Información de la Contingencia')
                    ->description('Detalles principales de la contingencia')
                    ->schema([
                        TextEntry::make('contingency_id')
                            ->label('Identificador'),
                        TextEntry::make('flight_number')
                            ->label('Número de Vuelo'),
                        TextEntry::make('contingency_type')
                            ->label('Tipo de Contingencia')
                            ->formatStateUsing(fn(string $state): string => Contingency::getContingencyTypes()[$state] ?? $state)
                            ->badge()
                            ->color(fn(string $state): string => match ($state) {
                                'cancelacion' => 'danger',
                                'retraso' => 'warning',
                                'sobre_venta' => 'info',
                                default => 'gray',
                            }),
                        TextEntry::make('scale')
                            ->label('Ruta')
                            ->placeholder('No especificada'),
                        TextEntry::make('date')
                            ->label('Fecha y Hora')
                            ->dateTime('d/m/Y H:i'),
                        IconEntry::make('finished')
                            ->label('Finalizada')
                            ->boolean()
                            ->trueIcon('heroicon-o-check-circle')
                            ->falseIcon('heroicon-o-exclamation-circle')
                            ->trueColor('success')
                            ->falseColor('danger'),
                    ])->columnSpan(3)->columns(3),

                InfolistSection::make('Ubicación y Aerolínea')
                    ->description('Información sobre la base y aerolínea asociada')
                    ->schema([
                        TextEntry::make('base.name')
                            ->label('Base'),
                        TextEntry::make('airline.name')
                            ->label('Aerolínea'),
                    ])->columnSpan(2),
            ])->columns(5);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('contingency_id')
                    ->label('ID Contingencia')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('flight_number')
                    ->label('N° Vuelo')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('scale')
                    ->label('Escala')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('base.name')
                    ->label('Base')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('airline.name')
                    ->label('Aerolínea')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('contingency_type')
                    ->label('Tipo')
                    ->formatStateUsing(fn(string $state): string => Contingency::getContingencyTypes()[$state] ?? $state)
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'cancelacion' => 'danger',
                        'retraso' => 'warning',
                        'sobre_venta' => 'info',
                        default => 'gray',
                    }),
                IconColumn::make('finished')
                    ->label('Estado')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-exclamation-triangle')
                    ->trueColor('success')
                    ->falseColor('warning')
                    ->tooltip(fn($record): string => $record->finished ? 'Finalizada' : 'Activa')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('user.name')
                    ->label('Creado por')
                    ->placeholder('Usuario eliminado')
                    ->searchable()
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('date')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Registrado')
                    ->since()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('contingency_type')
                    ->label('Tipo')
                    ->options(Contingency::getContingencyTypes()),
                SelectFilter::make('base_id')
                    ->label('Base')
                    ->options(function () {
                        // Solo bases asignadas al usuario actual
                        $user = User::find(Auth::id());
                        return $user ? $user->bases()->pluck('bases.name', 'bases.id') : [];
                    }),
                SelectFilter::make('airline_id')
                    ->label('Aerolínea')
                    ->options(function () {
                        // Solo aerolíneas de las bases del usuario actual
                        $user = User::find(Auth::id());
                        if (!$user) return [];

                        $userBaseIds = $user->bases()->pluck('bases.id');
                        return Airline::whereHas('bases', function ($query) use ($userBaseIds) {
                            $query->whereIn('bases.id', $userBaseIds);
                        })->pluck('name', 'id');
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
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
            ->with(['base', 'airline', 'user'])
            ->whereHas('base.users', function ($query) {
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
            'index' => Pages\ListContingencies::route('/'),
            'create' => Pages\CreateContingency::route('/create'),
            'view' => Pages\ViewContingency::route('/{record}'),
            'edit' => Pages\EditContingency::route('/{record}/edit'),
        ];
    }
}
