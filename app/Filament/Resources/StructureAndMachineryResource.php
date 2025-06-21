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
    protected static ?int $navigationSort = 3;

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
                    'comentarios' => '',
                    'observaciones' => '',
                    'imagenes' => [],
                ];
                $defaultItems[$part][] = [
                    'nombre_item' => 'En Puente de Mando',
                    'comentarios' => '',
                    'observaciones' => '',
                    'imagenes' => [],
                ];
            } elseif ($part === 4) {
                $defaultItems[$part][] = [
                    'nombre_item' => 'Ítem 4.1',
                    'comentarios' => '',
                    'observaciones' => '',
                    'imagenes' => [],
                ];
                $defaultItems[$part][] = [
                    'nombre_item' => 'Luces de Navegación',
                    'comentarios' => '',
                    'observaciones' => '',
                    'imagenes' => [],
                ];
                $defaultItems[$part][] = [
                    'nombre_item' => 'Equipos de Comunicación',
                    'comentarios' => '',
                    'observaciones' => '',
                    'imagenes' => [],
                ];
                $defaultItems[$part][] = [
                    'nombre_item' => 'Equipos de Navegación',
                    'comentarios' => '',
                    'observaciones' => '',
                    'imagenes' => [],
                ];
            } else {
                for ($i = 1; $i <= $count; $i++) {
                    $defaultItems[$part][] = [
                        'nombre_item' => "Ítem $part.$i",
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
                            'lg' => 4,
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
                                        $set('vessel_2_id', null);
                                        $set('vessel_3_id', null);
                                    })
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
                                        return \App\Models\Vessel::where('owner_id', $ownerId)->pluck('name', 'id');
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
                                        return \App\Models\Vessel::where('owner_id', $ownerId)->pluck('name', 'id');
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
                                        return \App\Models\Vessel::where('owner_id', $ownerId)->pluck('name', 'id');
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
                                    ->maxLength(255)
                                    ->columnSpan([
                                        'default' => 1,
                                        'md' => 2,
                                        'lg' => 2,
                                    ]),

                                Forms\Components\Select::make('overall_status')
                                    ->label('Estado General')
                                    ->options([
                                        'V' => 'V - Vigente (100% operativo, cumple, buenas condiciones)',
                                        'A' => 'A - En trámite (operativo con observaciones menores)',
                                        'N' => 'N - Reparaciones (observaciones que comprometen estanqueidad)',
                                        'R' => 'R - Vencido (inoperativo, no cumple, observaciones críticas)',
                                    ])
                                    ->required()
                                    ->default('A')
                                    ->columnSpan([
                                        'default' => 1,
                                        'md' => 1,
                                        'lg' => 1,
                                    ]),
                            ]),
                    ]),
                Tabs::make('Partes de Estructura y Maquinaria')
                    ->columnSpanFull()
                    ->tabs([
                        ...array_map(function ($i) use ($partNames, $itemsPerPart, $defaultItems) {
                            return Tabs\Tab::make($partNames[$i] ?? ('Parte ' . $i))
                                ->icon('heroicon-o-clipboard-document')
                                ->schema([
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
                        ->options([
                            'V' => 'V - Vigente (100% operativo, cumple, buenas condiciones)',
                            'A' => 'A - En trámite (operativo con observaciones menores)',
                            'N' => 'N - Reparaciones (observaciones que comprometen estanqueidad)',
                            'R' => 'R - Vencido (inoperativo, no cumple, observaciones críticas)',
                        ])
                        ->required(),
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
            // Establecer la cantidad de ítems iniciales y sus nombres
            ->default($defaultItems);
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
                    ->label('Estado')
                    ->colors([
                        'success' => 'V',
                        'warning' => 'A',
                        'danger' => ['N', 'R'],
                    ])
                    ->formatStateUsing(fn (string $state): string => static::getOverallStatusOptions()[$state] ?? $state),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->headerActions([
                Tables\Actions\Action::make('create')
                    ->label('Crear registro')
                    ->url(route('filament.admin.resources.structure-and-machineries.create'))
                    ->icon('heroicon-o-plus')
                    ->color('primary')
                    ->size('lg')
                    ->button(),
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
                    ->options(StatutoryCertificate::getOverallStatusOptions()),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'V' => 'V - Vigente (100% operativo, cumple, buenas condiciones)',
            'A' => 'A - En trámite (operativo con observaciones menores)',
            'N' => 'N - Reparaciones (observaciones que comprometen estanqueidad)',
            'R' => 'R - Vencido (inoperativo, no cumple, observaciones críticas)',
        ];
    }
}
