<?php

namespace App\Filament\Resources;

use App\Models\StructureAndMachinery;
use Filament\Forms;
use App\Models\StatutoryCertificate;
use App\Models\Vessel;
use App\Models\Owner;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\StructureAndMachineryResource\Pages;

class StructureAndMachineryResource extends Resource
{
    protected static ?string $model = StructureAndMachinery::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog';
    protected static ?string $navigationGroup = 'Inspecciones';
    protected static ?string $navigationLabel = 'Estructura y Maquinaria';
    protected static ?string $modelLabel = 'Estructura y Maquinaria';
    protected static ?string $pluralModelLabel = 'Estructuras y Maquinarias';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        // Nombres de las partes personalizables
        $partNames = [
            1 => 'Casco y Estructura',
            2 => 'Sistema de Propulsión',
            3 => 'Sistema de Gobierno',
            4 => 'Luces de Navegación y Equipos de Comunicación y Navegación',
            5 => 'Sistema Eléctrico',
            6 => 'Sistema de Combustible',
            7 => 'Sistema de Contraincendios',
            8 => 'Sistema de Achique y Sentina',
            9 => 'Sistema de Aguas Negras',
            10 => 'Sistema de Amarre',
            11 => 'Sistema de Agua Dulce y de Servicios Generales',
            12 => 'Seguridad y Salvamento',
            13 => 'Aspectos Generales',
        ];
        // Cantidad de ítems por parte
        $itemsPerPart = [
            1 => 1,
            2 => 2,
            3 => 1,
            4 => 4,
            5 => 1,
            6 => 1,
            7 => 1,
            8 => 1,
            9 => 1,
            10 => 1,
            11 => 1,
            12 => 1,
            13 => 1,
        ];
        // Nombres por defecto de los ítems
        $defaultItems = [];
        foreach ($itemsPerPart as $part => $count) {
            $defaultItems[$part] = [];
            if ($part === 2) {
                $defaultItems[$part][] = [
                    'nombre_item' => 'En Sala de Máquinas',
                    'estado' => 'V',
                    'comentarios' => '',
                    'observaciones' => '',
                    'imagenes' => [],
                ];
                $defaultItems[$part][] = [
                    'nombre_item' => 'En Puente de Mando',
                    'estado' => 'V',
                    'comentarios' => '',
                    'observaciones' => '',
                    'imagenes' => [],
                ];
            } elseif ($part === 4) {
                $defaultItems[$part][] = [
                    'nombre_item' => 'Ítem 4.1',
                    'estado' => 'V',
                    'comentarios' => '',
                    'observaciones' => '',
                    'imagenes' => [],
                ];
                $defaultItems[$part][] = [
                    'nombre_item' => 'Luces de Navegación',
                    'estado' => 'V',
                    'comentarios' => '',
                    'observaciones' => '',
                    'imagenes' => [],
                ];
                $defaultItems[$part][] = [
                    'nombre_item' => 'Equipos de Comunicación',
                    'estado' => 'V',
                    'comentarios' => '',
                    'observaciones' => '',
                    'imagenes' => [],
                ];
                $defaultItems[$part][] = [
                    'nombre_item' => 'Equipos de Navegación',
                    'estado' => 'V',
                    'comentarios' => '',
                    'observaciones' => '',
                    'imagenes' => [],
                ];
            } else {
                for ($i = 1; $i <= $count; $i++) {
                    $defaultItems[$part][] = [
                        'nombre_item' => "Ítem $part.$i",
                        'estado' => 'V',
                        'comentarios' => '',
                        'observaciones' => '',
                        'imagenes' => [],
                    ];
                }
            }
        }
        return $form
            ->schema([
                Section::make('Información General')
                    ->description('Datos básicos de la inspección')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make([
                            'default' => 1,
                            'sm' => 1,
                            'md' => 2,
                            'lg' => 3,
                        ])
                            ->schema([
                                Forms\Components\Select::make('owner_id')
                                    ->label('Propietario')
                                    ->options(\App\Models\Owner::all()->pluck('name', 'id'))
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->afterStateUpdated(function (Forms\Set $set) {
                                        $set('vessel_id', null);
                                        $set('associated_vessels', []);
                                    })
                                    ->columnSpan([
                                        'default' => 1,
                                        'md' => 1,
                                        'lg' => 1,
                                    ]),

                                Forms\Components\Select::make('vessel_id')
                                    ->label('Embarcación Principal')
                                    ->options(function (Forms\Get $get) {
                                        $ownerId = $get('owner_id');
                                        if (!$ownerId) {
                                            return [];
                                        }
                                        return \App\Models\Vessel::where('owner_id', $ownerId)->pluck('name', 'id');
                                    })
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->disabled(fn (Forms\Get $get): bool => !$get('owner_id'))
                                    ->afterStateUpdated(function (Forms\Set $set, $state) {
                                        if ($state) {
                                            // Obtener embarcaciones asociadas
                                            $vessel = \App\Models\Vessel::find($state);
                                            if ($vessel) {
                                                $associatedVessels = $vessel->getAllAssociatedVessels();
                                                $associatedIds = $associatedVessels->pluck('id')->toArray();
                                                $set('associated_vessels', $associatedIds);
                                            }
                                        } else {
                                            $set('associated_vessels', []);
                                        }
                                    })
                                    ->columnSpan([
                                        'default' => 1,
                                        'md' => 1,
                                        'lg' => 1,
                                    ]),

                                Forms\Components\Select::make('associated_vessels')
                                    ->label('Embarcaciones Asociadas')
                                    ->multiple()
                                    ->options(function () {
                                        return \App\Models\Vessel::pluck('name', 'id')->toArray();
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->helperText('Embarcaciones asociadas que se incluirán en la inspección. Se cargan automáticamente pero puedes modificarlas.')
                                    ->columnSpan([
                                        'default' => 1,
                                        'md' => 2,
                                        'lg' => 1,
                                    ]),

                                Forms\Components\DatePicker::make('inspection_date')
                                    ->label('Fecha de Inspección')
                                    ->required()
                                    ->default(now())
                                    ->columnSpan([
                                        'default' => 1,
                                        'md' => 1,
                                        'lg' => 1,
                                    ]),

                                Forms\Components\TextInput::make('inspector_name')
                                    ->label('Nombre del Inspector')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpan([
                                        'default' => 1,
                                        'md' => 1,
                                        'lg' => 1,
                                    ]),

                                Forms\Components\TextInput::make('inspector_license')
                                    ->label('Licencia del Inspector')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpan([
                                        'default' => 1,
                                        'md' => 1,
                                        'lg' => 1,
                                    ]),

                                Forms\Components\Select::make('overall_status')
                                    ->label('Estado General')
                                    ->options(static::getOverallStatusOptions())
                                    ->disabled()
                                    ->dehydrated()
                                    ->helperText('Se calcula automáticamente según los estados de todas las partes')
                                    ->columnSpan([
                                        'default' => 1,
                                        'md' => 2,
                                        'lg' => 2,
                                    ])
                                    ->afterStateHydrated(function (Forms\Set $set, Forms\Get $get, $state) {
                                        // Calcular estados iniciales cuando se carga el formulario
                                        static::calculateAllStates($set, $get);
                                    }),
                            ]),
                    ]),
                Tabs::make('Partes de Estructura y Maquinaria')
                    ->columnSpanFull()
                    ->tabs([
                        ...array_map(function ($i) use ($partNames, $itemsPerPart, $defaultItems) {
                            return Tabs\Tab::make($partNames[$i] ?? ('Parte ' . $i))
                                ->icon('heroicon-o-clipboard-document')
                                ->schema([
                                    // Estado de la parte
                                    Section::make('Estado de la Parte')
                                        ->description('Estado general de ' . ($partNames[$i] ?? ('Parte ' . $i)))
                                        ->schema([
                                            Forms\Components\Select::make('parte_' . $i . '_estado')
                                                ->label('Estado de ' . ($partNames[$i] ?? ('Parte ' . $i)))
                                                ->options(static::getOverallStatusOptions())
                                                ->disabled()
                                                ->dehydrated()
                                                ->helperText('Se calcula automáticamente según los estados de los ítems de esta parte')
                                                ->columnSpanFull(),
                                        ])
                                        ->columnSpanFull(),
                                    
                                    // Ítems de la parte
                                    static::createPartSection($i, $partNames[$i] ?? ('Parte ' . $i), $itemsPerPart[$i] ?? 1, $defaultItems[$i] ?? []),
                                ]);
                        }, range(1, 13)),
                    ]),
            ]);
    }

    // Ahora acepta nombre de parte, cantidad de ítems y valores por defecto
    protected static function createPartSection(int $partNumber, string $partName, int $itemsCount, array $defaultItems): Repeater
    {
        return Repeater::make('parte_' . $partNumber . '_items')
            ->label($partName)
            ->schema([
                Grid::make([
                    'default' => 1,
                    'md' => 4,
                ])->schema([
                    // Permitir editar el nombre del ítem
                    TextInput::make('nombre_item')
                        ->label('Nombre del Ítem')
                        ->required(),
                    // Estado igual que en Documentos Estatutarios
                    Forms\Components\Select::make('estado')
                        ->label('Estado')
                        ->options(static::getOverallStatusOptions())
                        ->required()
                        ->live()
                        ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get) use ($partNumber) {
                            // Recalcular el estado de la parte cuando cambie el estado de un ítem
                            static::updatePartStatus($set, $get, $partNumber);
                        }),
                    Textarea::make('comentarios')
                        ->label('Comentarios')
                        ->rows(2),
                    Textarea::make('observaciones')
                        ->label('Observaciones')
                        ->rows(2),
                    FileUpload::make('imagenes')
                        ->label('Imágenes')
                        ->multiple()
                        ->image()
                        ->directory('structure-machinery/part_' . $partNumber),
                ]),
            ])
            ->addActionLabel('Agregar ítem')
            ->reorderable(false)
            ->collapsible()
            ->itemLabel(fn (array $state): ?string => $state['nombre_item'] ?? 'Nuevo ítem')
            ->columnSpanFull()
            ->live()
            ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get) use ($partNumber) {
                // Recalcular el estado de la parte cuando se agreguen/eliminen ítems
                static::updatePartStatus($set, $get, $partNumber);
            })
            // Establecer la cantidad de ítems iniciales y sus nombres
            ->default($defaultItems);
    }

    /**
     * Actualiza el estado de una parte específica basado en los estados de sus ítems
     * Solo será "V" (Conforme General) cuando TODOS los ítems sean "V"
     */
    protected static function updatePartStatus(Forms\Set $set, Forms\Get $get, int $partNumber): void
    {
        $items = $get('parte_' . $partNumber . '_items') ?? [];
        $estados = [];
        
        foreach ($items as $item) {
            if (!empty($item['estado'])) {
                $estados[] = $item['estado'];
            }
        }
        
        // Si no hay ítems o no hay estados definidos, no calcular nada
        if (empty($items) || empty($estados)) {
            return;
        }
        
        // Solo será "V" si TODOS los ítems son "V"
        $todosConformes = true;
        foreach ($estados as $estado) {
            if ($estado !== 'V') {
                $todosConformes = false;
                break;
            }
        }
        
        $estadoParte = 'V'; // Por defecto será V si todos son V
        if (!$todosConformes) {
            // Si no todos son conformes, tomar el peor estado
            if (in_array('R', $estados, true)) {
                $estadoParte = 'R';
            } elseif (in_array('N', $estados, true)) {
                $estadoParte = 'N';
            } else {
                $estadoParte = 'A';
            }
        }
        
        $set('parte_' . $partNumber . '_estado', $estadoParte);
        
        // Actualizar el estado general
        static::updateOverallStatus($set, $get);
    }

    /**
     * Actualiza el estado general basado en los estados de todas las partes
     * Solo será "V" si TODAS las partes son "V"
     */
    protected static function updateOverallStatus(Forms\Set $set, Forms\Get $get): void
    {
        $estadosPartes = [];
        
        for ($i = 1; $i <= 13; $i++) {
            $estadoParte = $get('parte_' . $i . '_estado');
            if ($estadoParte) {
                $estadosPartes[] = $estadoParte;
            }
        }
        
        // Si no hay estados de partes definidos, no calcular nada
        if (empty($estadosPartes)) {
            return;
        }
        
        // Solo será "V" si TODAS las partes son "V"
        $todasConformes = true;
        foreach ($estadosPartes as $estado) {
            if ($estado !== 'V') {
                $todasConformes = false;
                break;
            }
        }
        
        $estadoGeneral = 'V'; // Por defecto será V si todas son V
        if (!$todasConformes) {
            // Si no todas son conformes, tomar el peor estado
            if (in_array('R', $estadosPartes, true)) {
                $estadoGeneral = 'R';
            } elseif (in_array('N', $estadosPartes, true)) {
                $estadoGeneral = 'N';
            } else {
                $estadoGeneral = 'A';
            }
        }
        
        $set('overall_status', $estadoGeneral);
    }

    /**
     * Calcula todos los estados (partes y general) cuando se carga el formulario
     */
    protected static function calculateAllStates(Forms\Set $set, Forms\Get $get): void
    {
        // Calcular estado de cada parte
        for ($i = 1; $i <= 13; $i++) {
            static::updatePartStatus($set, $get, $i);
        }
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
                    ->label('Fecha de Inspección')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('inspector_name')
                    ->label('Inspector')
                    ->searchable(),

                Tables\Columns\BadgeColumn::make('overall_status')
                    ->label('Estado General')
                    ->getStateUsing(fn (StructureAndMachinery $record): string => $record->calculateOverallStatus())
                    ->color(fn ($state) => match (trim(strtoupper($state))) {
                        'V' => 'success',
                        'A' => 'warning',
                        'N', 'R' => 'danger',
                        default => 'secondary',
                    })
                    ->formatStateUsing(fn (string $state): string => static::getOverallStatusOptions()[$state] ?? $state),

                Tables\Columns\TextColumn::make('partes_observadas')
                    ->label('Partes con Observaciones')
                    ->getStateUsing(function (StructureAndMachinery $record): string {
                        // Recalcular el estado general en tiempo real para la tabla
                        $estadoGeneral = $record->calculateOverallStatus();
                        return $estadoGeneral === 'V' ? '—' : $record->getPartesConObservacionesTexto();
                    })
                    ->color(function (StructureAndMachinery $record): string {
                        $estadoGeneral = $record->calculateOverallStatus();
                        return $estadoGeneral === 'V' ? 'success' : 'warning';
                    })
                    ->tooltip(function (StructureAndMachinery $record): ?string {
                        $estadoGeneral = $record->calculateOverallStatus();
                        $partes = $record->getPartesConObservaciones();
                        return $estadoGeneral !== 'V' && count($partes) > 3 
                            ? 'Partes con observaciones: ' . implode(', ', $partes)
                            : null;
                    })
                    ->wrap()
                    ->searchable(false)
                    ->sortable(false),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(fn () => !auth()->check() || !auth()->user()->hasRole('Armador')),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn () => !auth()->check() || !auth()->user()->hasRole('Armador')),
            ])
            ->headerActions([
                Tables\Actions\Action::make('create')
                    ->label('Crear registro')
                    ->url(route('filament.admin.resources.structure-and-machineries.create'))
                    ->icon('heroicon-o-plus')
                    ->color('primary')
                    ->size('lg')
                    ->button()
                    ->visible(fn () => !auth()->check() || !auth()->user()->hasRole('Armador')),
            ])
            ->filters([
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

                Tables\Filters\SelectFilter::make('overall_status')
                    ->label('Estado')
                    ->options(static::getOverallStatusOptions()),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ])
                ->visible(fn () => !auth()->check() || !auth()->user()->hasRole('Armador')),
            ])
            ->emptyStateIcon('heroicon-o-cog')
            ->emptyStateHeading('No hay registros de estructura y maquinaria')
            ->emptyStateDescription('Crea el primer registro para comenzar.')
            ->emptyStateActions([
                Tables\Actions\Action::make('create')
                    ->label('Crear registro')
                    ->url(route('filament.admin.resources.structure-and-machineries.create'))
                    ->icon('heroicon-o-plus')
                    ->color('primary')
                    ->size('lg')
                    ->button()
                    ->visible(fn () => !auth()->check() || !auth()->user()->hasRole('Armador')),
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
        return static::$model ? static::$model::count() : null;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStructureAndMachineries::route('/'),
            'create' => Pages\CreateStructureAndMachinery::route('/create'),
            'view' => Pages\ViewStructureAndMachinery::route('/{record}'),
            'edit' => Pages\EditStructureAndMachinery::route('/{record}/edit'),
        ];
    }

    public static function getOverallStatusOptions(): array
    {
        return [
            'V' => 'V - Conforme General',
            'A' => 'A - Conforme con Observaciones',
            'N' => 'N - No Conforme con Reparaciones',
            'R' => 'R - No Conforme Crítico',
        ];
    }
}
