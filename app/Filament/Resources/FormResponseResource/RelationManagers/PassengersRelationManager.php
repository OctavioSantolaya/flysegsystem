<?php

namespace App\Filament\Resources\FormResponseResource\RelationManagers;

use App\Models\Passenger;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PassengersRelationManager extends RelationManager
{
    protected static string $relationship = 'passengers';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $title = 'Pasajeros';

    protected static ?string $modelLabel = 'Pasajero';

    protected static ?string $pluralModelLabel = 'Pasajeros';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->label('Nombre'),
                TextInput::make('surname')
                    ->required()
                    ->label('Apellido'),
                TextInput::make('pnr')
                    ->required()
                    ->label('PNR'),
                TextInput::make('age')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(120)
                    ->label('Edad'),
                TextInput::make('email')
                    ->email()
                    ->label('Correo Electrónico')
                    ->nullable(),
                TextInput::make('phone')
                    ->label('Teléfono')
                    ->nullable(),
                TextInput::make('document_number')
                    ->label('Número de documento')
                    ->nullable(),
            ])->columns(3);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
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
                TextColumn::make('created_at')
                    ->label('Registrado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AssociateAction::make()
                    ->label('Asociar pasajero')
                    ->modalHeading('Asociar pasajero')
                    ->modalSubmitActionLabel('Asociar')
                    ->recordTitle(fn ($record) => $record->name . ' ' . $record->surname . ' | ' . $record->pnr)
                    ->recordSelectSearchColumns(['name', 'surname', 'pnr', 'email'])
                    ->recordSelectOptionsQuery(function (Builder $query, $livewire) {
                        // Filtrar solo pasajeros de la misma contingencia
                        $contingencyId = $livewire->getOwnerRecord()->contingency_id;
                        $query->where('contingency_id', $contingencyId);
                        
                        // Obtener el PNR del primer pasajero ya relacionado
                        $relatedPassengers = $livewire->getRelationship()->getResults();
                        $pnr = $relatedPassengers->first()?->pnr;

                        $query->whereNull('form_response_id');

                        if ($pnr) {
                            // Ordenar primero los que tengan el mismo PNR
                            $query->orderByRaw("CASE WHEN pnr = ? THEN 0 ELSE 1 END", [$pnr]);
                        }

                        return $query;
                    })
                    ->preloadRecordSelect(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Editar'),
                Tables\Actions\DissociateAction::make()
                    ->label('Desasociar')
                    ->icon('heroicon-o-x-mark')
                    ->color('warning'),
                Tables\Actions\DeleteAction::make()
                    ->label('Eliminar'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DissociateBulkAction::make()
                        ->label('Desasociar seleccionados')
                        ->icon('heroicon-o-x-mark')
                        ->color('warning'),
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Eliminar seleccionados'),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->description('Lista de pasajeros asociados a esta respuesta.');
    }
}
