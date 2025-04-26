<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceTypeResource\Pages;
use App\Models\ServiceType;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ServiceTypeResource extends Resource
{
    protected static ?string $model = ServiceType::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationGroup = 'Configuración';

    protected static ?string $navigationLabel = 'Tipos de Servicio';

    protected static ?string $modelLabel = 'Tipo de Servicio';

    protected static ?string $pluralModelLabel = 'Tipos de Servicio';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->columns(3)
            ->schema([
                Section::make('Información del Tipo de Servicio')
                    ->description('Detalles del tipo de servicio para embarcaciones')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->label('Nombre')
                            ->placeholder('Ej: Empujador')
                            ->helperText('Nombre del tipo de servicio'),

                        Forms\Components\TextInput::make('code')
                            ->required()
                            ->maxLength(255)
                            ->label('Código')
                            ->placeholder('Ej: EF')
                            ->helperText('Código abreviado del tipo de servicio'),

                        Forms\Components\Textarea::make('description')
                            ->maxLength(65535)
                            ->label('Descripción')
                            ->placeholder('Descripción detallada del tipo de servicio')
                            ->helperText('Información adicional sobre este tipo de servicio')
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
                    ->description(fn (ServiceType $record): string => $record->code)
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('code')
                    ->searchable()
                    ->sortable()
                    ->label('Código')
                    ->badge()
                    ->color('success'),

                Tables\Columns\TextColumn::make('description')
                    ->searchable()
                    ->limit(50)
                    ->tooltip(fn (ServiceType $record): ?string => $record->description)
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
                    ->tooltip('Editar tipo de servicio'),

                Tables\Actions\DeleteAction::make()
                    ->iconButton()
                    ->tooltip('Eliminar tipo de servicio'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateIcon('heroicon-o-tag')
            ->emptyStateHeading('No hay tipos de servicio registrados')
            ->emptyStateDescription('Crea tu primer tipo de servicio para comenzar a clasificar tus embarcaciones.')
            ->emptyStateActions([
                Tables\Actions\Action::make('create')
                    ->label('Crear tipo de servicio')
                    ->url(route('filament.admin.resources.service-types.create'))
                    ->icon('heroicon-o-plus')
                    ->button(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServiceTypes::route('/'),
            'create' => Pages\CreateServiceType::route('/create'),
            'edit' => Pages\EditServiceType::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'code', 'description'];
    }
}
