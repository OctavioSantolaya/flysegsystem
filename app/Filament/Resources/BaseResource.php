<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BaseResource\Pages;
use App\Filament\Resources\BaseResource\RelationManagers;
use App\Models\Base;
use App\Models\User;
use App\Models\Airline;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BaseResource extends Resource
{
    protected static ?string $model = Base::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    protected static ?string $navigationGroup = 'Administración';

    protected static ?string $modelLabel = 'Base';

    protected static ?string $pluralModelLabel = 'Bases';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Section::make('Información de la Base')
                    ->description('Información general sobre la base, incluyendo su ubicación y descripción.')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('location')
                            ->label('Ubicación')
                            ->maxLength(255),
                        Textarea::make('description')
                            ->label('Descripción')
                            ->rows(4)
                            ->columnSpanFull(),
                    ])->columnSpan(3)->columns(2),
                Section::make('Relaciones')
                    ->description('Asigna usuarios y aerolíneas a esta base.')
                    ->schema([
                        Select::make('users')
                            ->label('Usuarios Asignados')
                            ->multiple()
                            ->relationship('users', 'name')
                            ->preload(),
                        Select::make('airlines')
                            ->label('Aerolíneas Asignadas')
                            ->multiple()
                            ->relationship('airlines', 'name')
                            ->preload(),
                    ])->columnSpan(2),
            ])->columns(5);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('location')
                    ->label('Ubicación')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->placeholder('No establecido'),
                TextColumn::make('users_count')
                    ->label('Usuarios')
                    ->badge()
                    ->counts('users'),
                TextColumn::make('airlines_count')
                    ->label('Aerolíneas')
                    ->badge()
                    ->counts('airlines'),
                TextColumn::make('contingencies_count')
                    ->label('Contingencias')
                    ->badge()
                    ->counts('contingencies')
                    ->color('warning'),
                TextColumn::make('created_at')
                    ->label('Creado')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBases::route('/'),
            'create' => Pages\CreateBase::route('/create'),
            'view' => Pages\ViewBase::route('/{record}'),
            'edit' => Pages\EditBase::route('/{record}/edit'),
        ];
    }
}
