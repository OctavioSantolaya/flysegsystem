<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Administración';

    protected static ?string $modelLabel = 'Usuario';

    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();
        
        // Si el usuario autenticado es super_admin, mostrar todos los usuarios
        if ($user && $user->roles->pluck('name')->contains('super_admin')) {
            return parent::getEloquentQuery();
        }

        // Si no es super_admin, ocultar los usuarios super_admin
        return parent::getEloquentQuery()
            ->whereDoesntHave('roles', function ($query) {
                $query->where('name', 'super_admin');
            });
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->label('Correo Electrónico')
                    ->email()
                    ->required()
                    ->maxLength(255),
                TextInput::make('password')
                    ->label('Contraseña')
                    ->password()
                    ->required(fn($context) => $context === 'create')
                    ->placeholder(fn($context) => $context === 'edit' ? 'Modificar Contraseña' : null)
                    ->maxLength(255)
                    ->dehydrateStateUsing(fn($state) => !empty($state) ? bcrypt($state) : null)
                    ->dehydrated(fn($state) => filled($state)),
                Forms\Components\Select::make('roles')
                    ->label('Roles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->required()
                    ->options(function () {
                        $roles = \Spatie\Permission\Models\Role::whereIn('name', [
                            'administrador', 'operador', 'gestor'
                        ])->pluck('name', 'id');
                        
                        $user = Auth::user();
                        if ($user && $user->roles->pluck('name')->contains('super_admin')) {
                            $superAdminRole = \Spatie\Permission\Models\Role::where('name', 'super_admin')->first();
                            if ($superAdminRole) {
                                $roles = [$superAdminRole->id => 'super_admin'] + $roles->toArray();
                            }
                        }
                        
                        return $roles;
                    }),
                Select::make('bases')
                    ->label('Bases Asignadas')
                    ->multiple()
                    ->relationship('bases', 'name')
                    ->columnSpanFull()
                    ->preload(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label('Nombre'),
                TextColumn::make('email')
                    ->searchable()
                    ->label('Correo Electrónico'),
                TextColumn::make('bases_count')
                    ->label('Bases')
                    ->badge()
                    ->counts('bases'),
                TextColumn::make('roles.name')
                    ->label('Roles')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'super_admin' => 'danger',
                        'administrador' => 'warning',
                        'operador' => 'success',
                        'gestor' => 'info',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->label('Fecha de Creación')
                    ->date('d/m/Y')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('roles')
                    ->label('Rol')
                    ->relationship('roles', 'name'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([

                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageUsers::route('/'),
        ];
    }
}
