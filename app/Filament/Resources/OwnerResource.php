<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OwnerResource\Pages;
use App\Models\Owner;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OwnerResource extends Resource
{
    protected static ?string $model = Owner::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Configuración';

    protected static ?string $navigationLabel = 'Propietarios';

    protected static ?string $modelLabel = 'Propietario';

    protected static ?string $pluralModelLabel = 'Propietarios';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->columns(3)
            ->schema([
                Section::make('Información del Propietario')
                    ->description('Detalles del propietario de la embarcación')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->label('Nombre')
                            ->placeholder('Ej: Juan Pérez / Naviera S.A.')
                            ->helperText('Nombre completo del propietario'),

                        Forms\Components\Select::make('type')
                            ->required()
                            ->options([
                                'individual' => 'Persona Natural',
                                'company' => 'Persona Jurídica',
                            ])
                            ->label('Tipo')
                            ->helperText('Tipo de persona (natural o jurídica)'),

                        Forms\Components\TextInput::make('identity_document')
                            ->maxLength(255)
                            ->label('Documento de Identidad')
                            ->placeholder('Ej: DNI 12345678 / RUC 20123456789')
                            ->helperText('DNI, RUC u otro documento de identidad'),

                        Forms\Components\TextInput::make('contact')
                            ->maxLength(255)
                            ->label('Contacto')
                            ->placeholder('Ej: +51 987654321 / contacto@empresa.com')
                            ->helperText('Teléfono, email u otra información de contacto')
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

                Tables\Columns\TextColumn::make('type')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'individual' => 'info',
                        'company' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'individual' => 'Persona Natural',
                        'company' => 'Persona Jurídica',
                        default => $state,
                    })
                    ->label('Tipo')
                    ->icon('heroicon-o-user-group'),

                Tables\Columns\TextColumn::make('identity_document')
                    ->searchable()
                    ->label('Documento de Identidad')
                    ->icon('heroicon-o-identification'),

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
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'individual' => 'Persona Natural',
                        'company' => 'Persona Jurídica',
                    ])
                    ->label('Tipo'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->iconButton()
                    ->tooltip('Editar propietario'),

                Tables\Actions\DeleteAction::make()
                    ->iconButton()
                    ->tooltip('Eliminar propietario'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateIcon('heroicon-o-user-group')
            ->emptyStateHeading('No hay propietarios registrados')
            ->emptyStateDescription('Crea tu primer propietario para comenzar a registrar embarcaciones.')
            ->emptyStateActions([
                Tables\Actions\Action::make('create')
                    ->label('Crear propietario')
                    ->url(route('filament.admin.resources.owners.create'))
                    ->icon('heroicon-o-plus')
                    ->button(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOwners::route('/'),
            'create' => Pages\CreateOwner::route('/create'),
            'edit' => Pages\EditOwner::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'identity_document', 'contact'];
    }
}
