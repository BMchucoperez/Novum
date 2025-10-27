<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InspectionScheduleResource\Pages;
use App\Filament\Resources\InspectionScheduleResource\RelationManagers;
use App\Models\InspectionSchedule;
use App\Models\Vessel;
use App\Models\Owner;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InspectionScheduleResource extends Resource
{
    protected static ?string $model = InspectionSchedule::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $navigationGroup = 'Inspecciones';

    protected static ?string $navigationLabel = 'Programación de Inspecciones';

    protected static ?string $modelLabel = 'Inspección Programada';

    protected static ?string $pluralModelLabel = 'Inspecciones Programadas';

    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información de la Inspección')
                    ->description('Detalles básicos de la inspección programada')
                    ->columnSpanFull()
                    ->schema([
                        Forms\Components\Grid::make([
                            'default' => 1,
                            'sm' => 1,
                            'md' => 2,
                            'lg' => 3,
                        ])
                        ->schema([
                            Forms\Components\Select::make('owner_id')
                                ->label('🏢 Propietario')
                                ->options(Owner::all()->pluck('name', 'id'))
                                ->required()
                                ->searchable()
                                ->preload()
                                ->live()
                                ->prefixIcon('heroicon-o-building-office')
                                ->placeholder('Seleccione el propietario...')
                                ->afterStateUpdated(function (Forms\Set $set) {
                                    $set('vessel_id', null);
                                    $set('title', null); // Limpiar título cuando cambie propietario
                                })
                                ->columnSpan([
                                    'default' => 1,
                                    'md' => 1,
                                    'lg' => 1,
                                ]),

                            Forms\Components\Select::make('vessel_id')
                                ->label('🚢 Embarcación')
                                ->options(function (Forms\Get $get) {
                                    $ownerId = $get('owner_id');
                                    if (!$ownerId) {
                                        return [];
                                    }
                                    
                                    $query = Vessel::where('owner_id', $ownerId);
                                    
                                    // For Armador users, only show vessels assigned to their user account
                                    if (auth()->user() && auth()->user()->hasRole('Armador')) {
                                        $userId = auth()->id();
                                        $query->where('user_id', $userId);
                                    }
                                    
                                    return $query->pluck('name', 'id');
                                })
                                ->required()
                                ->searchable()
                                ->preload()
                                ->live()
                                ->prefixIcon('heroicon-o-rectangle-stack')
                                ->placeholder('Seleccione la embarcación...')
                                ->disabled(fn (Forms\Get $get): bool => !$get('owner_id'))
                                ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get, $state) {
                                    if (!$state) {
                                        $set('title', null);
                                        return;
                                    }
                                    
                                    // Auto-generar título basado en propietario y embarcación
                                    $ownerId = $get('owner_id');
                                    if ($ownerId) {
                                        $owner = Owner::find($ownerId);
                                        $vessel = Vessel::find($state);
                                        
                                        if ($owner && $vessel) {
                                            $title = "Inspección - {$owner->name} - {$vessel->name}";
                                            $set('title', $title);
                                        }
                                    }
                                })
                                ->columnSpan([
                                    'default' => 1,
                                    'md' => 1,
                                    'lg' => 1,
                                ]),

                            Forms\Components\TextInput::make('title')
                                ->label('🏷️ Título de la Inspección')
                                ->required()
                                ->maxLength(255)
                                ->prefixIcon('heroicon-o-tag')
                                ->placeholder('Se generará automáticamente...')
                                ->helperText('El título se genera automáticamente al seleccionar propietario y embarcación')
                                ->columnSpan([
                                    'default' => 1,
                                    'md' => 1,
                                    'lg' => 1,
                                ]),

                            Forms\Components\DateTimePicker::make('start_datetime')
                                ->required()
                                ->label('Fecha y Hora de Inicio')
                                ->seconds(false),

                            Forms\Components\DateTimePicker::make('end_datetime')
                                ->required()
                                ->label('Fecha y Hora de Finalización')
                                ->seconds(false),

                            Forms\Components\Select::make('inspector_name')
                                ->label('👷 Inspector Asignado')
                                ->options(function () {
                                    return User::role('Inspector')
                                        ->orderBy('name')
                                        ->pluck('name', 'name');
                                })
                                ->required()
                                ->searchable()
                                ->preload()
                                ->prefixIcon('heroicon-o-user')
                                ->placeholder('Seleccione el inspector...')
                                ->helperText('Solo se muestran usuarios con rol "Inspector"'),

                            Forms\Components\Select::make('location')
                                ->label('📍 Ubicación')
                                ->options([
                                    'Iquitos' => 'Iquitos',
                                    'Manaos' => 'Manaos',
                                ])
                                ->required()
                                ->searchable()
                                ->preload()
                                ->placeholder('Seleccione la ubicación...')
                                ->prefixIcon('heroicon-o-map-pin'),

                            Forms\Components\Select::make('status')
                                ->options(InspectionSchedule::getStatusOptions())
                                ->required()
                                ->default('scheduled')
                                ->label('Estado'),

                            Forms\Components\Textarea::make('description')
                                ->label('Descripción')
                                ->columnSpanFull(),
                        ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->label('Título'),

                Tables\Columns\TextColumn::make('owner.name')
                    ->searchable()
                    ->sortable()
                    ->label('Propietario'),

                Tables\Columns\TextColumn::make('vessel.name')
                    ->searchable()
                    ->sortable()
                    ->label('Embarcación'),

                Tables\Columns\TextColumn::make('start_datetime')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->label('Inicio'),

                Tables\Columns\TextColumn::make('end_datetime')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->label('Fin'),

                Tables\Columns\TextColumn::make('inspector_name')
                    ->searchable()
                    ->label('Inspector'),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'scheduled' => 'primary',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => InspectionSchedule::getStatusLabel($state))
                    ->label('Estado'),
            ])
            ->filters([
                Tables\Filters\Filter::make('owner_vessel_filter')
                    ->form([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('owner_id')
                                    ->label('Propietario')
                                    ->options(Owner::all()->pluck('name', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->afterStateUpdated(function (Forms\Set $set) {
                                        $set('vessel_id', null);
                                    }),
                                    
                                Forms\Components\Select::make('vessel_id')
                                    ->label('Embarcación')
                                    ->options(function (Forms\Get $get) {
                                        $ownerId = $get('owner_id');
                                        
                                        $query = Vessel::query();
                                        
                                        // Filtrar por propietario si está seleccionado
                                        if ($ownerId) {
                                            $query->where('owner_id', $ownerId);
                                        }
                                        
                                        // For Armador users, only show vessels assigned to their user account
                                        if (auth()->user() && auth()->user()->hasRole('Armador')) {
                                            $userId = auth()->id();
                                            $query->where('user_id', $userId);
                                        }
                                        
                                        return $query->pluck('name', 'id');
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->disabled(fn (Forms\Get $get): bool => !$get('owner_id'))
                                    ->helperText('Primero seleccione un propietario'),
                            ]),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['owner_id'],
                                fn (Builder $query, $ownerId): Builder => $query->where('owner_id', $ownerId),
                            )
                            ->when(
                                $data['vessel_id'],
                                fn (Builder $query, $vesselId): Builder => $query->where('vessel_id', $vesselId),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        
                        if ($data['owner_id'] ?? null) {
                            $owner = Owner::find($data['owner_id']);
                            $indicators['owner_id'] = 'Propietario: ' . $owner?->name;
                        }
                        
                        if ($data['vessel_id'] ?? null) {
                            $vessel = Vessel::find($data['vessel_id']);
                            $indicators['vessel_id'] = 'Embarcación: ' . $vessel?->name;
                        }
                        
                        return $indicators;
                    }),

                Tables\Filters\SelectFilter::make('status')
                    ->options(InspectionSchedule::getStatusOptions())
                    ->label('Estado'),
            ])
            ->actions([
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
            'index' => Pages\ListInspectionSchedules::route('/'),
            'create' => Pages\CreateInspectionSchedule::route('/create'),
            'edit' => Pages\EditInspectionSchedule::route('/{record}/edit'),
        ];
    }
}
