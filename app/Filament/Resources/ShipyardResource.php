<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ShipyardResource\Pages;
use App\Models\Shipyard;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ShipyardResource extends Resource
{
    protected static ?string $model = Shipyard::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationGroup = 'Configuración';

    protected static ?string $navigationLabel = 'Astilleros';

    protected static ?string $modelLabel = 'Astillero';

    protected static ?string $pluralModelLabel = 'Astilleros';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->columns(3)
            ->schema([
                Section::make('Información del Astillero')
                    ->description('Detalles del astillero constructor de embarcaciones')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->label('Nombre')
                            ->placeholder('Ej: Astillero Naval Callao')
                            ->helperText('Nombre del astillero constructor'),

                        Forms\Components\TextInput::make('location')
                            ->maxLength(255)
                            ->label('Ubicación')
                            ->placeholder('Ej: Callao, Perú')
                            ->helperText('Ciudad y país donde se encuentra el astillero'),

                        Forms\Components\TextInput::make('contact')
                            ->maxLength(255)
                            ->label('Contacto')
                            ->placeholder('Ej: +51 987654321 / info@astillero.com')
                            ->helperText('Información de contacto del astillero')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label('Nombre')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('location')
                    ->searchable()
                    ->sortable()
                    ->label('Ubicación')
                    ->icon('heroicon-o-map-pin'),

                Tables\Columns\TextColumn::make('contact')
                    ->searchable()
                    ->label('Contacto')
                    ->icon('heroicon-o-phone'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Fecha de Creación'),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Fecha de Actualización'),
            ])
            ->defaultSort('name', 'asc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->iconButton()
                    ->tooltip('Editar astillero'),

                Tables\Actions\DeleteAction::make()
                    ->iconButton()
                    ->tooltip('Eliminar astillero'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateIcon('heroicon-o-building-office-2')
            ->emptyStateHeading('No hay astilleros registrados')
            ->emptyStateDescription('Crea tu primer astillero para comenzar a registrar embarcaciones.')
            ->emptyStateActions([
                Tables\Actions\Action::make('create')
                    ->label('Crear astillero')
                    ->url(route('filament.admin.resources.shipyards.create'))
                    ->icon('heroicon-o-plus')
                    ->button(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListShipyards::route('/'),
            'create' => Pages\CreateShipyard::route('/create'),
            'edit' => Pages\EditShipyard::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'location', 'contact'];
    }
}
