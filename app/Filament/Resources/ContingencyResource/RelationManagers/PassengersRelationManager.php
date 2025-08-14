<?php

namespace App\Filament\Resources\ContingencyResource\RelationManagers;

use App\Models\Passenger;
use App\Models\FormResponse;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Notifications\Notification;
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
                    ->columnSpan(4)
                    ->label('Nombre'),
                TextInput::make('surname')
                    ->required()
                    ->columnSpan(4)
                    ->label('Apellido'),
                TextInput::make('pnr')
                    ->required()
                    ->columnSpan(4)
                    ->label('PNR'),
                TextInput::make('age')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(120)
                    ->columnSpan(2)
                    ->label('Edad'),
                TextInput::make('email')
                    ->email()
                    ->label('Correo Electrónico')
                    ->columnSpan(4)
                    ->nullable(),
                TextInput::make('phone')
                    ->label('Teléfono')
                    ->columnSpan(3)
                    ->nullable(),
                TextInput::make('document_number')
                    ->label('Número de documento')
                    ->columnSpan(3)
                    ->nullable(),
            ])->columns(12);
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
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Agregar Pasajero')
                    ->icon('heroicon-o-plus')
                    ->color('primary'),
            ])
            ->actions([
                Tables\Actions\Action::make('createFormResponse')
                    ->label('Crear Respuesta')
                    ->icon('heroicon-o-document-plus')
                    ->color('success')
                    ->visible(fn ($record) => $record->form_response_id === null)
                    ->action(function ($record) {
                        $formResponse = FormResponse::create([
                            'contingency_id' => $record->contingency_id, // Usar la contingencia del pasajero
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
                            
                        return redirect()->route('filament.admin.resources.form-responses.edit', $formResponse);
                    }),
                Tables\Actions\Action::make('editFormResponse')
                    ->label('Ver Respuesta')
                    ->icon('heroicon-o-document-text')
                    ->color('primary')
                    ->visible(fn ($record) => $record->form_response_id !== null)
                    ->url(fn ($record) => route('filament.admin.resources.form-responses.view', $record->form_response_id)),
                Tables\Actions\EditAction::make()
                    ->label('Editar'),
                Tables\Actions\DeleteAction::make()
                    ->label('Eliminar'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Eliminar seleccionados'),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->description('Lista de pasajeros afectados por esta contingencia');
    }
}
