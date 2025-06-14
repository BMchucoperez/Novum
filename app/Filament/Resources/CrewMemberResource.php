<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CrewMemberResource\Pages;
use App\Models\CrewMember;
use App\Models\Vessel;
use App\Models\Owner;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Grid;
use Illuminate\Database\Eloquent\Builder;

class CrewMemberResource extends Resource
{
    protected static ?string $model = CrewMember::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Inspecciones';

    protected static ?string $navigationLabel = 'Tripulantes';

    protected static ?string $modelLabel = 'Registro de Tripulantes';

    protected static ?string $pluralModelLabel = 'Registros de Tripulantes';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Información General')
                    ->description('Datos básicos del registro de tripulantes')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make([
                            'default' => 1,
                            'sm' => 1,
                            'md' => 2,
                            'lg' => 4,
                        ])
                            ->schema([
                                Forms\Components\Select::make('owner_id')
                                    ->label('Propietario')
                                    ->options(Owner::all()->pluck('name', 'id'))
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->afterStateUpdated(function (Forms\Set $set) {
                                        $set('vessel_id', null);
                                        $set('vessel_2_id', null);
                                        $set('vessel_3_id', null);
                                    })
                                    ->columnSpan([
                                        'default' => 1,
                                        'md' => 2,
                                        'lg' => 2,
                                    ]),

                                Forms\Components\DatePicker::make('inspection_date')
                                    ->label('Fecha')
                                    ->required()
                                    ->default(now())
                                    ->columnSpan([
                                        'default' => 1,
                                        'md' => 2,
                                        'lg' => 2,
                                    ]),

                                Forms\Components\Select::make('vessel_id')
                                    ->label('Embarcación 1')
                                    ->options(function (Forms\Get $get) {
                                        $ownerId = $get('owner_id');
                                        if (!$ownerId) {
                                            return [];
                                        }
                                        return Vessel::where('owner_id', $ownerId)->pluck('name', 'id');
                                    })
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->disabled(fn (Forms\Get $get): bool => !$get('owner_id'))
                                    ->helperText('Primero selecciona un propietario')
                                    ->columnSpan([
                                        'default' => 1,
                                        'md' => 2,
                                        'lg' => 2,
                                    ]),

                                Forms\Components\Select::make('vessel_2_id')
                                    ->label('Embarcación 2 (Opcional)')
                                    ->options(function (Forms\Get $get) {
                                        $ownerId = $get('owner_id');
                                        if (!$ownerId) {
                                            return [];
                                        }
                                        return Vessel::where('owner_id', $ownerId)->pluck('name', 'id');
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->disabled(fn (Forms\Get $get): bool => !$get('owner_id'))
                                    ->helperText('Embarcación adicional (opcional)')
                                    ->columnSpan([
                                        'default' => 1,
                                        'md' => 1,
                                        'lg' => 1,
                                    ]),

                                Forms\Components\Select::make('vessel_3_id')
                                    ->label('Embarcación 3 (Opcional)')
                                    ->options(function (Forms\Get $get) {
                                        $ownerId = $get('owner_id');
                                        if (!$ownerId) {
                                            return [];
                                        }
                                        return Vessel::where('owner_id', $ownerId)->pluck('name', 'id');
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->disabled(fn (Forms\Get $get): bool => !$get('owner_id'))
                                    ->helperText('Embarcación adicional (opcional)')
                                    ->columnSpan([
                                        'default' => 1,
                                        'md' => 1,
                                        'lg' => 1,
                                    ]),
                            ]),
                    ]),

                Section::make('Tripulantes')
                    ->description('Lista de tripulantes a bordo')
                    ->columnSpanFull()
                    ->schema([
                        Repeater::make('tripulantes')
                            ->label('')
                            ->schema([
                                Grid::make([
                                    'default' => 1,
                                    'sm' => 1,
                                    'md' => 3,
                                    'lg' => 3,
                                    'xl' => 3,
                                ])
                                    ->schema([
                                        Forms\Components\Select::make('cargo')
                                            ->label('Cargo')
                                            ->options(CrewMember::getCargoOptions())
                                            ->required()
                                            ->searchable()
                                            ->columnSpan([
                                                'default' => 1,
                                                'md' => 1,
                                                'lg' => 1,
                                            ]),

                                        Forms\Components\TextInput::make('nombre')
                                            ->label('Nombre')
                                            ->required()
                                            ->maxLength(255)
                                            ->columnSpan([
                                                'default' => 1,
                                                'md' => 1,
                                                'lg' => 1,
                                            ]),

                                        Forms\Components\TextInput::make('matricula')
                                            ->label('N° de Matrícula')
                                            ->maxLength(255)
                                            ->columnSpan([
                                                'default' => 1,
                                                'md' => 1,
                                                'lg' => 1,
                                            ]),

                                        Forms\Components\Textarea::make('comentarios')
                                            ->label('Comentarios')
                                            ->placeholder('Observaciones específicas del tripulante...')
                                            ->rows(2)
                                            ->columnSpan([
                                                'default' => 1,
                                                'md' => 3,
                                                'lg' => 3,
                                            ]),
                                    ]),
                            ])
                            ->defaultItems(1)
                            ->default(CrewMember::getDefaultStructure())
                            ->addActionLabel("Añadir tripulante")
                            ->reorderable(true)
                            ->collapsible()
                            ->itemLabel(function (array $state): ?string {
                                $cargo = $state['cargo'] ?? '';
                                $nombre = $state['nombre'] ?? '';
                                
                                if ($cargo && $nombre) {
                                    return "{$cargo} - {$nombre}";
                                } elseif ($cargo) {
                                    return $cargo;
                                } elseif ($nombre) {
                                    return $nombre;
                                } else {
                                    return 'Nuevo tripulante';
                                }
                            })
                            ->columnSpanFull(),
                    ]),

                Section::make('Observaciones Generales')
                    ->description('Comentarios adicionales sobre la tripulación')
                    ->columnSpanFull()
                    ->schema([
                        Forms\Components\Textarea::make('general_observations')
                            ->label('')
                            ->placeholder('Observaciones generales sobre la tripulación, certificaciones, etc...')
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('owner.name')
                    ->label('Propietario')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('vessel.name')
                    ->label('Embarcación 1')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('vessel2.name')
                    ->label('Embarcación 2')
                    ->searchable()
                    ->sortable()
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('vessel3.name')
                    ->label('Embarcación 3')
                    ->searchable()
                    ->sortable()
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('inspection_date')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_crew')
                    ->label('Total Tripulantes')
                    ->getStateUsing(fn (CrewMember $record): int => $record->total_crew)
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('officers_count')
                    ->label('Oficiales')
                    ->getStateUsing(fn (CrewMember $record): int => $record->officers_count)
                    ->badge()
                    ->color('success'),

                Tables\Columns\TextColumn::make('crew_count')
                    ->label('Tripulación')
                    ->getStateUsing(fn (CrewMember $record): int => $record->crew_count)
                    ->badge()
                    ->color('warning'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('owner_id')
                    ->label('Propietario')
                    ->options(Owner::all()->pluck('name', 'id'))
                    ->searchable(),

                Tables\Filters\SelectFilter::make('vessel_id')
                    ->label('Embarcación 1')
                    ->options(Vessel::all()->pluck('name', 'id'))
                    ->searchable(),

                Tables\Filters\SelectFilter::make('vessel_2_id')
                    ->label('Embarcación 2')
                    ->options(Vessel::all()->pluck('name', 'id'))
                    ->searchable(),

                Tables\Filters\SelectFilter::make('vessel_3_id')
                    ->label('Embarcación 3')
                    ->options(Vessel::all()->pluck('name', 'id'))
                    ->searchable(),

                Tables\Filters\Filter::make('inspection_date')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('Desde'),
                        Forms\Components\DatePicker::make('until')
                            ->label('Hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('inspection_date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('inspection_date', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateIcon('heroicon-o-users')
            ->emptyStateHeading('No hay registros de tripulantes')
            ->emptyStateDescription('Crea el primer registro de tripulantes para comenzar.')
            ->emptyStateActions([
                Tables\Actions\Action::make('create')
                    ->label('Crear registro')
                    ->url(route('filament.admin.resources.crew-members.create'))
                    ->icon('heroicon-o-plus')
                    ->color('primary')
                    ->size('lg')
                    ->button(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCrewMembers::route('/'),
            'create' => Pages\CreateCrewMember::route('/create'),
            'view' => Pages\ViewCrewMember::route('/{record}'),
            'edit' => Pages\EditCrewMember::route('/{record}/edit'),
        ];
    }
}
