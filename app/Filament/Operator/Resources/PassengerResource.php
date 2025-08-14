<?php

namespace App\Filament\Operator\Resources;

use App\Filament\Operator\Resources\PassengerResource\Pages;
use App\Filament\Resources\PassengerResource\RelationManagers;
use App\Models\Passenger;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class PassengerResource extends Resource
{
    protected static ?string $model = Passenger::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';

    protected static ?string $navigationGroup = 'Operaciones';

    protected static ?string $modelLabel = 'Pasajero';

    protected static ?int $navigationSort = 2;

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->label('Nombre')
                    ->columnSpan(3),
                TextInput::make('surname')
                    ->required()
                    ->label('Apellido')
                    ->columnSpan(3),
                TextInput::make('pnr')
                    ->required()
                    ->columnSpan(3)
                    ->label('PNR'),
                TextInput::make('age')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(120)
                    ->label('Edad')
                    ->columnSpan(3),
                TextInput::make('email')
                    ->email()
                    ->label('Email')
                    ->columnSpan(4)
                    ->nullable(),
                TextInput::make('phone')
                    ->label('Teléfono')
                    ->columnSpan(4)
                    ->nullable(),
                TextInput::make('document_number')
                    ->columnSpan(4)
                    ->label('Número de documento')
                    ->nullable(),
                Select::make('contingency_id')
                    ->label('Contingencia Asociada')
                    ->relationship('contingency', 'contingency_id')
                    ->required()
                    ->searchable()
                    ->columnSpan(12)
                    ->preload(),
            ])->columns(12);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre'),
                Tables\Columns\TextColumn::make('surname')
                    ->label('Apellido'),
                Tables\Columns\TextColumn::make('pnr')
                    ->label('PNR'),
                Tables\Columns\TextColumn::make('contingency.contingency_id')
                    ->badge()
                    ->label('Contingencia'),
                Tables\Columns\TextColumn::make('age')
                    ->label('Edad')
                    ->badge()
                    ->color(fn ($record) => $record->age < 18 ? 'warning' : 'success')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->placeholder('No establecido')
                    ->toggleable(isToggledHiddenByDefault: true),
                    Tables\Columns\TextColumn::make('phone')
                    ->label('Teléfono')
                    ->placeholder('No establecido')
                    ->toggleable(isToggledHiddenByDefault: true),
                    Tables\Columns\TextColumn::make('document_number')
                    ->label('Documento')
                    ->placeholder('No establecido')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
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
            ->with(['contingency.base'])
            ->whereHas('contingency.base.users', function ($query) {
                $query->where('user_id', Auth::id());
            });
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManagePassengers::route('/'),
        ];
    }
}
