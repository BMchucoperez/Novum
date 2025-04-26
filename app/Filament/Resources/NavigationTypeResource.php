<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NavigationTypeResource\Pages;
use App\Models\NavigationType;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class NavigationTypeResource extends Resource
{
    protected static ?string $model = NavigationType::class;

    protected static ?string $navigationIcon = 'heroicon-o-map';

    protected static ?string $navigationGroup = 'Configuración';

    protected static ?string $navigationLabel = 'Tipos de Navegación';

    protected static ?string $modelLabel = 'Tipo de Navegación';

    protected static ?string $pluralModelLabel = 'Tipos de Navegación';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->columns(3)
            ->schema([
                Section::make('Información del Tipo de Navegación')
                    ->description('Detalles del tipo de navegación para embarcaciones')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->label('Nombre')
                            ->placeholder('Ej: Navegación Fluvial')
                            ->helperText('Nombre del tipo de navegación'),

                        Forms\Components\Textarea::make('description')
                            ->maxLength(65535)
                            ->label('Descripción')
                            ->placeholder('Descripción detallada del tipo de navegación')
                            ->helperText('Información adicional sobre este tipo de navegación')
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

                Tables\Columns\TextColumn::make('description')
                    ->searchable()
                    ->limit(50)
                    ->tooltip(fn (NavigationType $record): ?string => $record->description)
                    ->label('Descripción'),

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
                    ->tooltip('Editar tipo de navegación'),

                Tables\Actions\DeleteAction::make()
                    ->iconButton()
                    ->tooltip('Eliminar tipo de navegación'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateIcon('heroicon-o-map')
            ->emptyStateHeading('No hay tipos de navegación registrados')
            ->emptyStateDescription('Crea tu primer tipo de navegación para comenzar a clasificar tus embarcaciones.')
            ->emptyStateActions([
                Tables\Actions\Action::make('create')
                    ->label('Crear tipo de navegación')
                    ->url(route('filament.admin.resources.navigation-types.create'))
                    ->icon('heroicon-o-plus')
                    ->button(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNavigationTypes::route('/'),
            'create' => Pages\CreateNavigationType::route('/create'),
            'edit' => Pages\EditNavigationType::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'description'];
    }
}
