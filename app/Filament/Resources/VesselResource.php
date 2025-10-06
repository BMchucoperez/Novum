<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VesselResource\Pages;
use App\Filament\Resources\VesselResource\RelationManagers;
use App\Models\Vessel;
use App\Models\VesselDocumentType;
use App\Models\VesselDocument;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Group;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Layout\Grid as ColumnGrid;
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\HtmlString;

class VesselResource extends Resource
{
    protected static ?string $model = Vessel::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    protected static ?string $navigationGroup = 'Gestión de Embarcaciones';

    protected static ?string $navigationLabel = 'Embarcaciones';

    protected static ?string $modelLabel = 'Embarcación';

    protected static ?string $pluralModelLabel = 'Embarcaciones';

    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        // Si el usuario tiene el rol "Armador", solo mostrar sus embarcaciones asignadas
        if (auth()->check() && auth()->user()->hasRole('Armador')) {
            $query->where('user_id', auth()->id());
        }

        return $query;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->columns(3)
            ->schema([
                Tabs::make('Embarcación')
                    ->columnSpanFull()
                    ->persistTabInQueryString()
                    ->tabs([
                        Tab::make('Información General')
                            ->icon('heroicon-o-information-circle')
                            ->badge('Requerido')
                            ->badgeColor('danger')
                            ->schema([
                                Section::make('Datos Principales')
                                    ->description('Información básica de identificación de la embarcación')
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->required()
                                            ->maxLength(255)
                                            ->label('Nombre de la Embarcación')
                                            ->placeholder('Ej: RODRIGO XX')
                                            ->helperText('Nombre oficial registrado de la embarcación'),

                                        Forms\Components\TextInput::make('registration_number')
                                            ->required()
                                            ->maxLength(255)
                                            ->label('Número de Matrícula')
                                            ->placeholder('Ej: PA-64978-EF')
                                            ->helperText('Código único de registro oficial'),
                                    ])
                                    ->columns(2),

                                Section::make('Clasificación')
                                    ->description('Tipo de embarcación y navegación')
                                    ->schema([
                                        Forms\Components\Select::make('service_type_id')
                                            ->relationship('serviceType', 'name')
                                            ->required()
                                            ->searchable()
                                            ->preload()
                                            ->createOptionForm([
                                                Forms\Components\TextInput::make('name')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->label('Nombre'),
                                                Forms\Components\TextInput::make('code')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->label('Código'),
                                                Forms\Components\Textarea::make('description')
                                                    ->maxLength(65535)
                                                    ->label('Descripción'),
                                            ])
                                            ->label('Tipo de Embarcación')
                                            ->helperText('Seleccione el tipo de embarcación'),

                                        Forms\Components\Select::make('navigation_type_id')
                                            ->relationship('navigationType', 'name')
                                            ->required()
                                            ->searchable()
                                            ->preload()
                                            ->createOptionForm([
                                                Forms\Components\TextInput::make('name')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->label('Nombre'),
                                                Forms\Components\Textarea::make('description')
                                                    ->maxLength(65535)
                                                    ->label('Descripción'),
                                            ])
                                            ->label('Tipo de Navegación')
                                            ->helperText('Seleccione el tipo de navegación autorizada'),
                                    ])
                                    ->columns(2),

                                Section::make('Registro')
                                    ->description('Información oficial de registro')
                                    ->schema([
                                        Forms\Components\Select::make('flag_registry')
                                            ->required()
                                            ->options([
                                                'Perú' => 'Perú',
                                                'Brasil' => 'Brasil',
                                            ])
                                            ->label('Bandera de Registro')
                                            ->helperText('País de registro de la embarcación'),

                                        Forms\Components\TextInput::make('port_registry')
                                            ->required()
                                            ->maxLength(255)
                                            ->label('Puerto de Registro')
                                            ->placeholder('Ej: Pucallpa')
                                            ->helperText('Puerto donde está registrada la embarcación'),
                                    ])
                                    ->columns(2),

                                Section::make('Propietario')
                                    ->description('Información del propietario de la embarcación')
                                    ->schema([
                                        Forms\Components\Select::make('owner_id')
                                            ->relationship('owner', 'name')
                                            ->required()
                                            ->searchable()
                                            ->preload()
                                            ->createOptionForm([
                                                Forms\Components\TextInput::make('name')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->label('Nombre'),
                                                Forms\Components\Select::make('type')
                                                    ->required()
                                                    ->options([
                                                        'individual' => 'Persona Natural',
                                                        'company' => 'Persona Jurídica',
                                                    ])
                                                    ->label('Tipo'),
                                                Forms\Components\TextInput::make('identity_document')
                                                    ->maxLength(255)
                                                    ->label('Documento de Identidad'),
                                                Forms\Components\TextInput::make('contact')
                                                    ->maxLength(255)
                                                    ->label('Contacto'),
                                            ])
                                            ->label('Propietario')
                                            ->helperText('Seleccione el propietario legal de la embarcación'),
                                    ]),

                                Section::make('Usuario Asignado')
                                    ->description('Asignar un usuario responsable de la embarcación')
                                    ->schema([
                                        Forms\Components\Select::make('user_id')
                                            ->relationship('user', 'name', function ($query) {
                                                // Solo mostrar usuarios con el rol "Armador"
                                                return $query->whereHas('roles', function ($query) {
                                                    $query->where('name', 'Armador');
                                                });
                                            })
                                            ->searchable()
                                            ->preload()
                                            ->label('Usuario Asignado')
                                            ->helperText('Seleccione el usuario responsable de esta embarcación (solo usuarios con rol Armador)')
                                            ->placeholder('Seleccione un usuario'),
                                    ]),
                            ]),

                        Tab::make('Características Técnicas')
                            ->icon('heroicon-o-wrench-screwdriver')
                            ->badge('Requerido')
                            ->badgeColor('danger')
                            ->schema([
                                Section::make('Construcción')
                                    ->description('Detalles de construcción de la embarcación')
                                    ->schema([
                                        Forms\Components\TextInput::make('construction_year')
                                            ->required()
                                            ->numeric()
                                            ->minValue(1900)
                                            ->maxValue(date('Y'))
                                            ->label('Año de Construcción')
                                            ->placeholder(date('Y'))
                                            ->helperText('Año en que se construyó la embarcación'),

                                        Forms\Components\Select::make('shipyard_id')
                                            ->relationship('shipyard', 'name')
                                            ->required()
                                            ->searchable()
                                            ->preload()
                                            ->createOptionForm([
                                                Forms\Components\TextInput::make('name')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->label('Nombre'),
                                                Forms\Components\TextInput::make('location')
                                                    ->maxLength(255)
                                                    ->label('Ubicación'),
                                                Forms\Components\TextInput::make('contact')
                                                    ->maxLength(255)
                                                    ->label('Contacto'),
                                            ])
                                            ->label('Astillero')
                                            ->helperText('Astillero donde se construyó la embarcación'),
                                    ])
                                    ->columns(2),

                                Section::make('Dimensiones')
                                    ->description('Medidas principales de la embarcación')
                                    ->schema([
                                        Forms\Components\TextInput::make('length')
                                            ->required()
                                            ->numeric()
                                            ->minValue(0)
                                            ->step(0.01)
                                            ->suffix('m')
                                            ->label('Eslora')
                                            ->placeholder('0.00')
                                            ->helperText('Longitud de proa a popa'),

                                        Forms\Components\TextInput::make('beam')
                                            ->required()
                                            ->numeric()
                                            ->minValue(0)
                                            ->step(0.01)
                                            ->suffix('m')
                                            ->label('Manga')
                                            ->placeholder('0.00')
                                            ->helperText('Anchura máxima'),

                                        Forms\Components\TextInput::make('depth')
                                            ->required()
                                            ->numeric()
                                            ->minValue(0)
                                            ->step(0.01)
                                            ->suffix('m')
                                            ->label('Puntal')
                                            ->placeholder('0.00')
                                            ->helperText('Altura desde la quilla hasta la cubierta'),
                                    ])
                                    ->columns(3),

                                Section::make('Capacidad')
                                    ->description('Información sobre la capacidad de la embarcación')
                                    ->schema([
                                        Forms\Components\TextInput::make('gross_tonnage')
                                            ->required()
                                            ->numeric()
                                            ->minValue(0)
                                            ->step(0.01)
                                            ->label('Arqueo Bruto')
                                            ->placeholder('0.00')
                                            ->helperText('Volumen total de todos los espacios cerrados del buque')
                                            ->suffix('ton'),
                                    ]),

                        
                                Section::make('Documentos Anexos - Vista Rápida')
                                    ->description(function ($record) {
                                        $count = $record ? $record->vesselDocuments()->count() : 0;
                                        return "Vista rápida de los {$count} documentos anexados. Para una vista completa, ve a la pestaña 'Documentos Cargados'.";
                                    })
                                    ->schema([
                                        static::createDocumentsList()
                                    ])
                                    ->collapsible()
                                    ->collapsed(),
                            ]),

                        Tab::make('Embarcaciones Asociadas')
                            ->icon('heroicon-o-link')
                            ->schema([
                                Section::make('Asociaciones')
                                    ->description('Embarcaciones que se incluirán automáticamente en las inspecciones cuando selecciones esta embarcación principal')
                                    ->schema([
                                        Forms\Components\Select::make('associated_vessels')
                                            ->label('Embarcaciones Asociadas')
                                            ->multiple()
                                            ->searchable()
                                            ->preload()
                                            ->options(function ($record) {
                                                // Excluir la embarcación actual de las opciones
                                                $query = \App\Models\Vessel::query();
                                                if ($record) {
                                                    $query->where('id', '!=', $record->id);
                                                }
                                                return $query->pluck('name', 'id');
                                            })
                                            ->helperText('Selecciona las embarcaciones que se incluirán automáticamente en las inspecciones. Máximo 2 embarcaciones adicionales.')
                                            ->maxItems(2)
                                            ->afterStateHydrated(function ($component, $record) {
                                                if ($record) {
                                                    $associatedIds = $record->associatedVessels()->pluck('associated_vessel_id')->toArray();
                                                    $component->state($associatedIds);
                                                }
                                            })
                                            ->dehydrated(false),
                                    ]),
                            ]),

                        Tab::make('Documentos y Certificados Obligatorios')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                Section::make('DOCUMENTOS DE BANDEIRA E APOLICES DE SEGURO')
                                    ->description('Documentos obligatorios relacionados con bandeira y pólizas de seguro')
                                    ->schema([
                                        static::createDocumentUploadGrid('bandeira_apolices')
                                    ]),

                                Section::make('DOCUMENTOS DO SISTEMA DE GESTÃO DE BORDO')
                                    ->description('Documentos del sistema de gestión a bordo')
                                    ->schema([
                                        static::createDocumentUploadGrid('sistema_gestao')
                                    ]),

                                Section::make('DOCUMENTOS EXCLUSIVOS POR TIPO DE EMBARCACIÓN')
                                    ->description('Documentos específicos según el tipo de embarcación')
                                    ->schema([
                                        static::createExclusiveDocumentUploadGrid()
                                    ]),
                            ]),

                        Tab::make('Documentos Cargados')
                            ->schema([
                                Section::make('Documentos')
                                    ->schema([
                                        Forms\Components\Repeater::make('existing_documents')
                                            ->relationship('vesselDocuments')
                                            ->schema([
                                                Forms\Components\TextInput::make('document_name')
                                                    ->label('Documento')
                                                    ->disabled(),
                                                Forms\Components\Actions::make([
                                                    Forms\Components\Actions\Action::make('download')
                                                        ->label('Descargar')
                                                        ->url(function ($record) {
                                                            if ($record && $record->file_path && file_exists(storage_path('app/public/' . $record->file_path))) {
                                                                return \Illuminate\Support\Facades\Storage::disk('public')->url($record->file_path);
                                                            }
                                                            return null;
                                                        })
                                                        ->openUrlInNewTab()
                                                        ->disabled(function ($record) {
                                                            return !($record && $record->file_path && file_exists(storage_path('app/public/' . $record->file_path)));
                                                        }),
                                                ]),
                                            ])
                                            ->columns(2)
                                            ->addable(false)
                                            ->deletable(false)
                                            ->reorderable(false),
                                    ]),
                            ]),
                    ])
                    ->extraAttributes(['class' => 'bg-white'])
                    ->contained(false)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Columnas principales (siempre visibles)
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label('Nombre')
                    ->weight('bold')
                    ->formatStateUsing(fn (string $state): string => strtoupper($state))
                    ->description(fn (Vessel $record): string => $record->registration_number)
                    ->color('primary'),

                Tables\Columns\TextColumn::make('serviceType.name')
                    ->searchable()
                    ->sortable()
                    ->label('Tipo de Embarcación')
                    ->badge()
                    ->color('success'),

                Tables\Columns\TextColumn::make('flag_registry')
                    ->searchable()
                    ->sortable()
                    ->label('Bandera')
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->description(fn (Vessel $record): string => $record->port_registry)
                    ->icon('heroicon-o-flag'),

                Tables\Columns\TextColumn::make('owner.name')
                    ->searchable()
                    ->sortable()
                    ->label('Propietario / Armador')
                    ->limit(30)
                    ->icon('heroicon-o-user'),

                // Tables\Columns\TextColumn::make('user.name')
                //     ->searchable()
                //     ->sortable()
                //     ->label('Usuario Asignado')
                //     ->limit(30)
                //     ->icon('heroicon-o-user-circle'),

                // Tables\Columns\TextColumn::make('associated_vessels_count')
                //     ->label('Embarcaciones Asociadas')
                //     ->state(function (Vessel $record): string {
                //         $count = $record->associatedVessels()->count();
                //         if ($count === 0) {
                //             return 'Ninguna';
                //         }
                //         return $count . ' asociada' . ($count > 1 ? 's' : '');
                //     })
                //     ->badge()
                //     ->color(fn (string $state): string => $state === 'Ninguna' ? 'gray' : 'success')
                //     ->icon('heroicon-o-link'),

                Tables\Columns\TextColumn::make('documents_completeness')
                    ->label('Documentos')
                    ->state(function (Vessel $record): string {
                        return $record->documents()->count() . ' documentos';
                    })
                    ->badge()
                    ->color('primary')
                    ->icon('heroicon-o-document-text'),

                Tables\Columns\TextColumn::make('construction_year')
                    ->sortable()
                    ->label('Año de Construcción')
                    ->alignCenter()
                    ->icon('heroicon-o-calendar'),

                // Columnas adicionales (ocultas por defecto)                
                Tables\Columns\TextColumn::make('port_registry')
                    ->searchable()
                    ->label('Puerto')
                    ->icon('heroicon-o-map-pin')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('length')
                    ->label('Eslora')
                    ->formatStateUsing(fn (float $state): string => "{$state}m")
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('beam')
                    ->label('Manga')
                    ->formatStateUsing(fn (float $state): string => "{$state}m")
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('depth')
                    ->label('Puntal')
                    ->formatStateUsing(fn (float $state): string => "{$state}m")
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('gross_tonnage')
                    ->numeric()
                    ->sortable()
                    ->label('Arqueo Bruto')
                    ->suffix(' ton')
                    ->icon('heroicon-o-scale')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('shipyard.name')
                    ->searchable()
                    ->label('Astillero')
                    ->limit(30)
                    ->icon('heroicon-o-building-office-2')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Fecha de Creación'),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Fecha de Actualización'),
            ])
            ->paginated([10, 25, 50, 'all'])
            ->defaultPaginationPageOption(10)
            ->defaultSort('name', 'asc')
            ->striped()
            ->searchable()
            ->searchPlaceholder('Buscar embarcaciones...')
            ->searchDebounce(500)
            ->deferFilters()
            ->filtersFormColumns(3)
            ->filtersLayout(Tables\Enums\FiltersLayout::AboveContent)
            ->header(
                view('filament.components.vessel-header', ['totalVessels' => Vessel::count()])
            )
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Nueva Embarcación')
                    ->icon('heroicon-o-plus')
                    ->color('primary')
                    ->size('md')
                    ->button(),
                Tables\Actions\Action::make('export-all')
                    ->label('Exportar Todo')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('gray')
                    ->action(fn () => redirect()->back()),
            ])
            ->toggleColumnsTriggerAction(
                fn (Tables\Actions\Action $action) => $action
                    ->button()
                    ->label('Columnas')
                    ->icon('heroicon-o-view-columns')
                    ->color('gray')
            )
            ->filters([
                Tables\Filters\SelectFilter::make('service_type_id')
                    ->relationship('serviceType', 'name')
                    ->label('Tipo de Embarcación')
                    ->indicator('Tipo de Embarcación')
                    ->multiple()
                    ->preload()
                    ->searchable(),

                Tables\Filters\SelectFilter::make('owner_id')
                    ->relationship('owner', 'name')
                    ->label('Propietario / Armador')
                    ->searchable()
                    ->preload(),

                // Tables\Filters\SelectFilter::make('shipyard_id')
                //     ->relationship('shipyard', 'name')
                //     ->label('Astillero')
                //     ->preload()
                //     ->searchable(),

                // Tables\Filters\SelectFilter::make('user_id')
                //     ->relationship('user', 'name', function ($query) {
                //         // Solo mostrar usuarios con el rol "Armador"
                //         return $query->whereHas('roles', function ($query) {
                //             $query->where('name', 'Armador');
                //         });
                //     })
                //     ->label('Usuario Asignado')
                //     ->preload()
                //     ->searchable(),
            ])
            ->filtersFormColumns(3)
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->color('info')
                        ->icon('heroicon-o-eye'),
                    Tables\Actions\EditAction::make()
                        ->color('warning')
                        ->icon('heroicon-o-pencil'),
                    Tables\Actions\DeleteAction::make()
                        ->color('danger')
                        ->icon('heroicon-o-trash'),
                    Tables\Actions\Action::make('certificate')
                        ->label('Certificados')
                        ->color('success')
                        ->icon('heroicon-o-document-check')
                        ->url(fn (Vessel $record): string => route('filament.admin.resources.vessels.view', ['record' => $record])),
                ])
                ->tooltip('Acciones')
                ->icon('heroicon-m-ellipsis-vertical')
                ->color('gray')
                ->size('sm')
                ->iconButton(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->icon('heroicon-o-trash')
                        ->color('danger'),

                    Tables\Actions\BulkAction::make('export')
                        ->label('Exportar seleccionados')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('success')
                        ->action(fn (Collection $records) => redirect()->back())
                        ->deselectRecordsAfterCompletion(),

                    Tables\Actions\BulkAction::make('print')
                        ->label('Imprimir seleccionados')
                        ->icon('heroicon-o-printer')
                        ->color('info')
                        ->action(fn (Collection $records) => redirect()->back())
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->emptyStateIcon('heroicon-o-truck')
            ->emptyStateHeading('No hay embarcaciones registradas')
            ->emptyStateDescription('Crea tu primera embarcación para comenzar a gestionar tu flota.')
            ->emptyStateActions([
                Tables\Actions\Action::make('create')
                    ->label('Crear embarcación')
                    ->url(route('filament.admin.resources.vessels.create'))
                    ->icon('heroicon-o-plus')
                    ->color('primary')
                    ->size('lg')
                    ->button(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // Aquí se pueden agregar relaciones como certificados, documentos, etc.
        ];
    }

    // public static function getNavigationBadge(): ?string
    // {
    //     return static::getModel()::count();
    // }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'registration_number', 'flag_registry', 'port_registry'];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVessels::route('/'),
            'create' => Pages\CreateVessel::route('/create'),
            'view' => Pages\ViewVessel::route('/{record}'),
            'edit' => Pages\EditVessel::route('/{record}/edit'),
        ];
    }

    /**
     * Crear grilla de documentos para una categoría específica
     */
    protected static function createDocumentUploadGrid(string $category): Grid
    {
        $documents = $category === 'bandeira_apolices' 
            ? VesselDocumentType::getBandeiraApolicesDocuments()
            : VesselDocumentType::getSistemaGestaoDocuments();

        $fields = [];
        
        foreach ($documents as $documentType => $documentName) {
            $fields[] = Forms\Components\FileUpload::make("document_{$documentType}")
                ->label($documentName)
                ->disk('public')
                ->directory(function ($record) use ($category) {
                    if ($record && $record->id) {
                        // Embarcación existente
                        return "vessel-documents/{$record->id}/{$category}";
                    } else {
                        // Nueva embarcación - usar directorio temporal
                        return "vessel-documents/temp/{$category}";
                    }
                })
                ->acceptedFileTypes(['application/pdf', 'image/png'])
                ->maxSize(10240) // 10MB
                ->helperText('Solo PDF y PNG. Máximo 10MB.')
                ->afterStateHydrated(function ($state, $set, $record) use ($documentType) {
                    if ($record) {
                        $document = $record->getDocumentByType($documentType);
                        if ($document && $document->file_path) {
                            $set("document_{$documentType}", [$document->file_path]);
                        }
                    }
                })
                ->afterStateUpdated(function ($state, $record, $set, $component) use ($documentType, $category, $documentName) {
                    $fieldName = "document_{$documentType}";

                    Log::info('📤 ARCHIVO SUBIDO AL COMPONENTE', [
                        'vessel_id' => $record ? $record->id : 'null',
                        'document_type' => $documentType,
                        'field_name' => $fieldName,
                        'state_count' => is_array($state) ? count($state) : (empty($state) ? 0 : 1),
                    ]);

                    if ($record && !empty($state)) {
                        // Normalizar state a array para procesamiento uniforme
                        $stateArray = is_array($state) ? $state : [$state];

                        // Detectar archivos nuevos vs existentes
                        $hasNewFiles = false;
                        $newFileDetected = null;

                        foreach ($stateArray as $index => $file) {
                            if ($file && !is_string($file)) {
                                $hasNewFiles = true;
                                $newFileDetected = $file;
                                break;
                            }
                        }

                        // Si hay archivos nuevos, procesar solo el más reciente (último subido)
                        if ($hasNewFiles && $newFileDetected) {
                            Log::info("📂 PROCESANDO ARCHIVO NUEVO", [
                                'vessel_id' => $record->id,
                                'document_type' => $documentType,
                                'file_name' => method_exists($newFileDetected, 'getClientOriginalName') ? $newFileDetected->getClientOriginalName() : 'unknown',
                            ]);

                            try {
                                // Procesar el archivo nuevo (esto reemplazará el existente)
                                static::handleDocumentUpload($newFileDetected, $record, $documentType, $category, $documentName);

                                Log::info("✅ ARCHIVO PROCESADO EXITOSAMENTE", [
                                    'vessel_id' => $record->id,
                                    'document_type' => $documentType,
                                ]);

                                // Actualizar el estado del componente con el archivo guardado
                                $document = $record->getDocumentByType($documentType);
                                if ($document) {
                                    $component->state([$document->file_path]);
                                    Log::info('🔄 ESTADO DEL COMPONENTE ACTUALIZADO', [
                                        'vessel_id' => $record->id,
                                        'document_type' => $documentType,
                                        'new_file_path' => $document->file_path,
                                    ]);
                                }

                            } catch (\Exception $e) {
                                Log::error("❌ ERROR PROCESANDO ARCHIVO", [
                                    'vessel_id' => $record->id,
                                    'document_type' => $documentType,
                                    'error' => $e->getMessage(),
                                ]);
                            }
                        }
                    }
                })
                ->downloadable()
                ->openable()
                ->deletable(true)
                ->dehydrated(false);
        }
        
        return Grid::make(2)->schema($fields);
    }

    /**
     * Manejar la subida de documentos usando el enfoque correcto de Filament
     */
    protected static function handleDocumentUpload($file, $vessel, $documentType, $category, $documentName): void
    {
        if (!$file || !$vessel) {
            return;
        }

        try {
            // Eliminar documento existente si hay uno
            $existingDocument = $vessel->getDocumentByType($documentType);
            if ($existingDocument) {
                $existingDocument->delete();
            }

            $disk = Storage::disk('public');
            
            // Si es un TemporaryUploadedFile (objeto de Livewire)
            if (is_object($file) && method_exists($file, 'store')) {
                Log::info("📄 GUARDANDO ARCHIVO PDF: {$file->getClientOriginalName()}");
                
                // Generar un nombre único
                $extension = $file->getClientOriginalExtension();
                $newFileName = $documentType . '_' . $vessel->id . '_' . time() . '.' . $extension;
                $storagePath = "vessel-documents/{$vessel->id}/{$category}/{$newFileName}";
                
                // Usar el método store de Livewire en disco público
                $finalPath = $file->storeAs("vessel-documents/{$vessel->id}/{$category}", $newFileName, 'public');
                
                $fileSize = $file->getSize();
                $mimeType = $file->getMimeType();
                $fileName = $newFileName;
                
            } elseif (is_string($file) && file_exists($file)) {
                // Es una ruta de archivo temporal
                Log::info("📄 GUARDANDO ARCHIVO TEMPORAL: {$file}");
                
                $fileInfo = pathinfo($file);
                $extension = strtolower($fileInfo['extension'] ?? 'pdf');
                
                // Validar extensión
                if (!in_array($extension, ['pdf', 'png', 'jpg', 'jpeg'])) {
                    throw new \Exception("Tipo de archivo no permitido: {$extension}");
                }
                
                $content = file_get_contents($file);
                if ($content === false) {
                    throw new \Exception("No se pudo leer el archivo: {$file}");
                }
                
                $newFileName = $documentType . '_' . $vessel->id . '_' . time() . '.' . $extension;
                $finalPath = "vessel-documents/{$vessel->id}/{$category}/{$newFileName}";
                
                $success = $disk->put($finalPath, $content);
                if (!$success) {
                    throw new \Exception("No se pudo guardar el archivo");
                }
                
                $fileSize = strlen($content);
                $mimeType = match($extension) {
                    'pdf' => 'application/pdf',
                    'png' => 'image/png',
                    'jpg', 'jpeg' => 'image/jpeg',
                    default => 'application/pdf'
                };
                $fileName = $newFileName;
                
            } else {
                throw new \Exception("Formato de archivo no reconocido");
            }

            // Crear registro en base de datos
            $vesselDocument = VesselDocument::create([
                'vessel_id' => $vessel->id,
                'document_type' => $documentType,
                'document_category' => $category,
                'document_name' => $documentName,
                'file_path' => $finalPath,
                'file_name' => $fileName,
                'file_size' => $fileSize,
                'mime_type' => $mimeType,
                'uploaded_at' => now(),
                'is_valid' => true,
            ]);

            Log::info("✅ DOCUMENTO GUARDADO EN BD", [
                'vessel_document_id' => $vesselDocument->id,
                'document_type' => $documentType,
                'file_path' => $finalPath,
                'vessel_id' => $vessel->id,
            ]);
            
        } catch (\Exception $e) {
            Log::error("❌ ERROR GUARDANDO DOCUMENTO", [
                'document_type' => $documentType,
                'vessel_id' => $vessel ? $vessel->id : 'null',
                'error' => $e->getMessage(),
            ]);

            \Filament\Notifications\Notification::make()
                ->title('Error al subir documento')
                ->body("Error con {$documentName}: " . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    /**
     * Crear grilla de documentos exclusivos por tipo de embarcación
     */
    protected static function createExclusiveDocumentUploadGrid(): Grid
    {
        $fields = [];
        
        // Documentos exclusivos para Barcazas
        $barcazaDocuments = VesselDocumentType::getBarcazaExclusiveDocuments();
        if (!empty($barcazaDocuments)) {
            $fields[] = Forms\Components\Section::make('Documentos Exclusivos para Barcazas')
                ->description('Documentos específicos para embarcaciones tipo Barcaza')
                ->schema(static::createDocumentFields($barcazaDocuments, 'barcaza_exclusive'))
                ->collapsible()
                ->collapsed(false);
        }

        // Documentos exclusivos para Empujadores
        $empujadorDocuments = VesselDocumentType::getEmpujadorExclusiveDocuments();
        if (!empty($empujadorDocuments)) {
            $fields[] = Forms\Components\Section::make('Documentos Exclusivos para Empujadores')
                ->description('Documentos específicos para embarcaciones tipo Empujador')
                ->schema(static::createDocumentFields($empujadorDocuments, 'empujador_exclusive'))
                ->collapsible()
                ->collapsed(false);
        }

        // Documentos exclusivos para Motochatas
        $motochataDocuments = VesselDocumentType::getMotochataExclusiveDocuments();
        if (!empty($motochataDocuments)) {
            $fields[] = Forms\Components\Section::make('Documentos Exclusivos para Motochatas')
                ->description('Documentos específicos para embarcaciones tipo Motochata')
                ->schema(static::createDocumentFields($motochataDocuments, 'motochata_exclusive'))
                ->collapsible()
                ->collapsed(false);
        }
        
        return Grid::make(1)->schema($fields);
    }

    /**
     * Crear campos de documentos para una categoría específica
     */
    protected static function createDocumentFields(array $documents, string $category): array
    {
        $fields = [];
        
        foreach ($documents as $documentType => $documentName) {
            $fields[] = Forms\Components\FileUpload::make("document_{$documentType}")
                ->label($documentName)
                ->disk('public')
                ->directory(function ($record) use ($category) {
                    if ($record && $record->id) {
                        // Embarcación existente
                        return "vessel-documents/{$record->id}/{$category}";
                    } else {
                        // Nueva embarcación - usar directorio temporal
                        return "vessel-documents/temp/{$category}";
                    }
                })
                ->acceptedFileTypes(['application/pdf', 'image/png'])
                ->maxSize(10240) // 10MB
                ->helperText('Solo PDF y PNG. Máximo 10MB.')
                ->afterStateHydrated(function ($component, $record) use ($documentType, $category, $documentName) {
                    Log::info('🔍 ===== AFTERSTATEHYDRATED INICIADO =====', [
                        'vessel_id' => $record ? $record->id : 'null',
                        'vessel_name' => $record ? $record->name : 'null',
                        'document_type' => $documentType,
                        'document_name' => $documentName,
                        'category' => $category,
                        'field_name' => "document_{$documentType}",
                    ]);

                    if ($record) {
                        $document = $record->getDocumentByType($documentType);

                        Log::info('📄 BUSCANDO DOCUMENTO EN BD', [
                            'vessel_id' => $record->id,
                            'document_type' => $documentType,
                            'document_found' => !empty($document),
                            'document_id' => $document ? $document->id : 'null',
                            'document_file_path' => $document ? $document->file_path : 'null',
                            'document_file_name' => $document ? $document->file_name : 'null',
                        ]);

                        if ($document && $document->file_path) {
                            // Verificar si el archivo existe físicamente - probar ambas ubicaciones
                            $publicPath = storage_path('app/public/' . $document->file_path);
                            $privatePath = storage_path('app/' . $document->file_path);

                            $fileExists = false;
                            $fullPath = '';
                            $diskType = '';

                            // Primero probar en disco público (nueva ubicación)
                            if (file_exists($publicPath)) {
                                $fileExists = true;
                                $fullPath = $publicPath;
                                $diskType = 'public';
                            }
                            // Si no existe, probar en disco local (ubicación antigua)
                            elseif (file_exists($privatePath)) {
                                $fileExists = true;
                                $fullPath = $privatePath;
                                $diskType = 'local';
                            }

                            $filePermissions = $fileExists ? substr(sprintf('%o', fileperms($fullPath)), -4) : 'N/A';
                            $fileSize = $fileExists ? filesize($fullPath) : 'N/A';

                            Log::info('💾 VERIFICACIÓN FÍSICA DEL ARCHIVO', [
                                'vessel_id' => $record->id,
                                'document_type' => $documentType,
                                'db_file_path' => $document->file_path,
                                'public_path_checked' => $publicPath,
                                'private_path_checked' => $privatePath,
                                'file_found_in' => $diskType,
                                'full_storage_path' => $fullPath,
                                'file_exists' => $fileExists,
                                'file_permissions' => $filePermissions,
                                'file_size_bytes' => $fileSize,
                                'expected_public_url' => $diskType === 'public' ? url('storage/' . $document->file_path) : 'N/A (private file)',
                            ]);

                            // Para FileUpload, el estado debe ser un array de archivos
                            $component->state([$document->file_path]);

                            Log::info('✅ COMPONENT STATE CONFIGURADO', [
                                'vessel_id' => $record->id,
                                'document_type' => $documentType,
                                'component_state' => [$document->file_path],
                                'state_count' => 1,
                            ]);
                        } else {
                            // Si no hay documento, estado vacío
                            $component->state([]);

                            Log::info('❌ NO HAY DOCUMENTO - STATE VACÍO', [
                                'vessel_id' => $record->id,
                                'document_type' => $documentType,
                                'reason' => !$document ? 'no_document_in_db' : 'no_file_path',
                            ]);
                        }
                    } else {
                        Log::warning('⚠️ NO HAY RECORD EN AFTERSTATEHYDRATED', [
                            'document_type' => $documentType,
                            'reason' => 'record_is_null'
                        ]);
                    }

                    Log::info('🔍 ===== AFTERSTATEHYDRATED COMPLETADO =====', [
                        'vessel_id' => $record ? $record->id : 'null',
                        'document_type' => $documentType,
                    ]);
                })
                ->afterStateUpdated(function ($state, $record, $set, $component) use ($documentType, $category, $documentName) {
                    $startTime = microtime(true);
                    $fieldName = "document_{$documentType}";

                    Log::info('📤 ===== FILEUPLOAD AFTERSTATEUPDATED INICIADO =====', [
                        'vessel_id' => $record ? $record->id : 'null',
                        'vessel_name' => $record ? $record->name : 'null',
                        'field_name' => $fieldName,
                        'document_type' => $documentType,
                        'document_name' => $documentName,
                        'category' => $category,
                        'state_type' => gettype($state),
                        'state_count' => is_array($state) ? count($state) : (empty($state) ? 0 : 1),
                        'has_record' => !empty($record),
                        'has_state' => !empty($state),
                        'timestamp' => now()->toDateTimeString(),
                        'memory_usage' => memory_get_usage(true),
                        'user_id' => auth()->id(),
                    ]);

                    if ($record && !empty($state)) {
                        Log::info('🔍 ANALIZANDO ARCHIVOS EN STATE', [
                            'vessel_id' => $record->id,
                            'field_name' => $fieldName,
                            'state_details' => is_array($state) ?
                                array_map(function($file, $index) {
                                    return [
                                        'index' => $index,
                                        'type' => gettype($file),
                                        'is_string' => is_string($file),
                                        'is_object' => is_object($file),
                                        'class' => is_object($file) ? get_class($file) : 'not_object',
                                        'original_name' => is_object($file) && method_exists($file, 'getClientOriginalName') ? $file->getClientOriginalName() : 'unknown',
                                        'size' => is_object($file) && method_exists($file, 'getSize') ? $file->getSize() : 'unknown',
                                        'will_process' => $file && !is_string($file)
                                    ];
                                }, $state, array_keys($state)) :
                                [[
                                    'index' => 0,
                                    'type' => gettype($state),
                                    'is_string' => is_string($state),
                                    'is_object' => is_object($state),
                                    'class' => is_object($state) ? get_class($state) : 'not_object',
                                    'original_name' => is_object($state) && method_exists($state, 'getClientOriginalName') ? $state->getClientOriginalName() : 'unknown',
                                    'size' => is_object($state) && method_exists($state, 'getSize') ? $state->getSize() : 'unknown',
                                    'will_process' => $state && !is_string($state)
                                ]]
                        ]);

                        $processedCount = 0;
                        $skippedCount = 0;
                        $errors = [];

                        // Normalizar state a array para procesamiento uniforme
                        $stateArray = is_array($state) ? $state : [$state];
                        $totalFiles = count($stateArray);

                        // Detectar archivos nuevos vs existentes
                        $hasNewFiles = false;
                        $newFileDetected = null;

                        foreach ($stateArray as $index => $file) {
                            if ($file && !is_string($file)) {
                                $hasNewFiles = true;
                                $newFileDetected = $file;
                                break;
                            }
                        }

                        Log::info("🔍 ANÁLISIS DE ARCHIVOS EN STATE", [
                            'vessel_id' => $record->id,
                            'field_name' => $fieldName,
                            'total_files' => $totalFiles,
                            'has_new_files' => $hasNewFiles,
                            'has_new_upload' => !empty($newFileDetected),
                        ]);

                        // Si hay archivos nuevos, procesar solo el más reciente (último subido)
                        if ($hasNewFiles && $newFileDetected) {
                            Log::info("📂 PROCESANDO ARCHIVO NUEVO DETECTADO", [
                                'vessel_id' => $record->id,
                                'field_name' => $fieldName,
                                'file_type' => gettype($newFileDetected),
                                'file_class' => is_object($newFileDetected) ? get_class($newFileDetected) : 'not_object',
                                'action' => 'replace_existing_document',
                            ]);

                            try {
                                // Procesar el archivo nuevo (esto reemplazará el existente)
                                static::handleDocumentUpload($newFileDetected, $record, $documentType, $category, $documentName);
                                $processedCount++;

                                Log::info("✅ ARCHIVO NUEVO PROCESADO Y REEMPLAZADO", [
                                    'vessel_id' => $record->id,
                                    'field_name' => $fieldName,
                                    'action' => 'document_replaced',
                                ]);

                                // Actualizar el estado del componente con el archivo guardado
                                $document = $record->getDocumentByType($documentType);
                                if ($document) {
                                    $component->state([$document->file_path]);
                                    Log::info('🔄 COMPONENT STATE ACTUALIZADO DESPUÉS DE GUARDAR', [
                                        'vessel_id' => $record->id,
                                        'field_name' => $fieldName,
                                        'new_file_path' => $document->file_path,
                                        'document_id' => $document->id
                                    ]);
                                }

                            } catch (\Exception $e) {
                                $errors[] = [
                                    'file_type' => 'new_upload',
                                    'error' => $e->getMessage()
                                ];

                                Log::error("❌ ERROR PROCESANDO ARCHIVO NUEVO", [
                                    'vessel_id' => $record->id,
                                    'field_name' => $fieldName,
                                    'error_message' => $e->getMessage(),
                                    'error_trace' => $e->getTraceAsString(),
                                ]);
                            }
                        } else {
                            // Solo archivos existentes, no hacer nada
                            $skippedCount = $totalFiles;
                            Log::info("⏭️ SOLO ARCHIVOS EXISTENTES DETECTADOS", [
                                'vessel_id' => $record->id,
                                'field_name' => $fieldName,
                                'reason' => 'no_new_uploads_detected',
                                'existing_files' => array_map(function($file) {
                                    return is_string($file) ? basename($file) : 'unknown';
                                }, $stateArray),
                            ]);
                        }

                        $endTime = microtime(true);
                        $processingTime = ($endTime - $startTime) * 1000;

                        Log::info('📤 ===== FILEUPLOAD AFTERSTATEUPDATED COMPLETADO =====', [
                            'vessel_id' => $record->id,
                            'field_name' => $fieldName,
                            'total_files' => $totalFiles,
                            'processed_files' => $processedCount,
                            'skipped_files' => $skippedCount,
                            'errors_count' => count($errors),
                            'errors' => $errors,
                            'processing_time_ms' => round($processingTime, 2),
                            'success' => count($errors) === 0,
                        ]);

                    } else {
                        Log::warning('⚠️ CALLBACK SIN PROCESAR', [
                            'vessel_id' => $record ? $record->id : 'null',
                            'field_name' => $fieldName,
                            'has_record' => !empty($record),
                            'has_state' => !empty($state),
                            'reason' => !$record ? 'no_record' : 'empty_state'
                        ]);
                    }
                })
                ->downloadable()
                ->openable()
                ->deletable(true)
                ->dehydrated(true);
        }
        
        return [Grid::make(2)->schema($fields)];
    }

    /**
     * Crear lista de documentos existentes
     */
    protected static function createDocumentsList()
    {
        return Forms\Components\Repeater::make('existing_documents')
            ->label('Documentos Existentes')
            ->relationship('vesselDocuments')
            ->schema([
                // Primera fila: Información principal
                Forms\Components\Group::make()->schema([
                    Forms\Components\TextInput::make('document_name')
                        ->label('Documento')
                        ->disabled()
                        ->formatStateUsing(fn ($state) => $state)
                        ->extraAttributes(['style' => 'font-weight: bold;'])
                        ->columnSpan(2),
                    
                    Forms\Components\TextInput::make('document_category')
                        ->label('Categoría')
                        ->disabled()
                        ->formatStateUsing(fn (string $state): string => match($state) {
                            'bandeira_apolices' => 'Bandeira e Apólices',
                            'sistema_gestao' => 'Sistema de Gestão',
                            'barcaza_exclusive' => 'Barcaza Exclusivo',
                            'empujador_exclusive' => 'Empujador Exclusivo',
                            'motochata_exclusive' => 'Motochata Exclusivo',
                            default => $state,
                        })
                        ->extraAttributes(function ($state) {
                            $colors = [
                                'bandeira_apolices' => '#3b82f6',
                                'sistema_gestao' => '#10b981',
                                'barcaza_exclusive' => '#f59e0b',
                                'empujador_exclusive' => '#06b6d4',
                                'motochata_exclusive' => '#6b7280',
                            ];
                            $color = $colors[$state] ?? '#6b7280';
                            return [
                                'style' => "background-color: {$color}; color: white; padding: 2px 8px; border-radius: 9999px; font-size: 0.75rem; font-weight: 500; text-align: center;"
                            ];
                        }),

                    Forms\Components\TextInput::make('status')
                        ->label('Estado')
                        ->disabled()
                        ->formatStateUsing(function ($state, $record) {
                            if (!$record) return 'Desconocido';
                            return $record->getStatusText();
                        })
                        ->extraAttributes(function ($state, $record) {
                            if (!$record) return ['style' => 'background-color: #6b7280; color: white; padding: 2px 8px; border-radius: 9999px; font-size: 0.75rem; font-weight: 500; text-align: center;'];
                            
                            $color = match($record->getStatusColor()) {
                                'success' => '#10b981',
                                'warning' => '#f59e0b',
                                'danger' => '#ef4444',
                                'primary' => '#3b82f6',
                                'info' => '#06b6d4',
                                'secondary' => '#6b7280',
                                default => '#6b7280',
                            };
                            
                            return [
                                'style' => "background-color: {$color}; color: white; padding: 2px 8px; border-radius: 9999px; font-size: 0.75rem; font-weight: 500; text-align: center;"
                            ];
                        }),
                ])->columns(4),
                
                // Segunda fila: Detalles del archivo
                Forms\Components\Group::make()->schema([
                    Forms\Components\TextInput::make('file_name')
                        ->label('Nombre del Archivo')
                        ->disabled()
                        ->formatStateUsing(fn ($state) => $state)
                        ->hint('Haz clic en el botón de descarga para obtener el archivo')
                        ->hintIcon('heroicon-o-information-circle')
                        ->columnSpan(1),
                    
                    Forms\Components\TextInput::make('file_size')
                        ->label('Tamaño')
                        ->disabled()
                        ->formatStateUsing(fn (int $state): string => $state ? number_format($state / 1024 / 1024, 2) . ' MB' : '')
                        ->columnSpan(1),
                    
                    Forms\Components\TextInput::make('uploaded_at')
                        ->label('Fecha de Subida')
                        ->disabled()
                        ->formatStateUsing(fn ($state): string => $state ? \Carbon\Carbon::parse($state)->format('d/m/Y H:i') : '')
                        ->columnSpan(1),
                    
                    Forms\Components\Actions::make([
                        Forms\Components\Actions\Action::make('download')
                            ->label(function ($record) {
                                if (!$record || !$record->file_path) {
                                    return 'No disponible';
                                }
                                return 'Descargar';
                            })
                            ->icon('heroicon-o-arrow-down-tray')
                            ->color(function ($record) {
                                if (!$record || !$record->file_path) {
                                    return 'gray';
                                }

                                // Verificar ubicación del archivo
                                $publicPath = storage_path('app/public/' . $record->file_path);
                                $privatePath = storage_path('app/' . $record->file_path);

                                if (file_exists($publicPath)) {
                                    return 'success';
                                } elseif (file_exists($privatePath)) {
                                    return 'warning';
                                } else {
                                    return 'danger';
                                }
                            })
                            ->url(function ($record) {
                                if (!$record || !$record->file_path) {
                                    return null;
                                }

                                // Verificar ubicación del archivo
                                $publicPath = storage_path('app/public/' . $record->file_path);

                                if (file_exists($publicPath)) {
                                    return \Illuminate\Support\Facades\Storage::disk('public')->url($record->file_path);
                                }

                                return null;
                            })
                            ->openUrlInNewTab()
                            ->disabled(function ($record) {
                                if (!$record || !$record->file_path) {
                                    return true;
                                }

                                // Solo habilitar si el archivo existe en ubicación pública
                                $publicPath = storage_path('app/public/' . $record->file_path);
                                return !file_exists($publicPath);
                            })
                            ->tooltip(function ($record) {
                                if (!$record || !$record->file_path) {
                                    return 'Archivo no disponible';
                                }

                                $publicPath = storage_path('app/public/' . $record->file_path);
                                $privatePath = storage_path('app/' . $record->file_path);

                                if (file_exists($publicPath)) {
                                    return 'Descargar archivo: ' . basename($record->file_path);
                                } elseif (file_exists($privatePath)) {
                                    return 'Archivo en ubicación privada - necesita migración';
                                } else {
                                    return 'Archivo no encontrado';
                                }
                            }),

                        Forms\Components\Actions\Action::make('view_info')
                            ->label('Info')
                            ->icon('heroicon-o-information-circle')
                            ->color('gray')
                            ->action(function ($record) {
                                // Esta acción mostrará información del archivo
                                $publicPath = storage_path('app/public/' . $record->file_path);
                                $privatePath = storage_path('app/' . $record->file_path);

                                $status = 'No encontrado';
                                $location = 'N/A';
                                $size = 'N/A';

                                if (file_exists($publicPath)) {
                                    $status = 'Disponible (Público)';
                                    $location = 'storage/app/public/';
                                    $size = number_format(filesize($publicPath) / 1024 / 1024, 2) . ' MB';
                                } elseif (file_exists($privatePath)) {
                                    $status = 'Disponible (Privado)';
                                    $location = 'storage/app/private/';
                                    $size = number_format(filesize($privatePath) / 1024 / 1024, 2) . ' MB';
                                }

                                // Mostrar notificación con información
                                \Filament\Notifications\Notification::make()
                                    ->title('Información del Archivo')
                                    ->body("Archivo: {$record->file_name}<br>Estado: {$status}<br>Ubicación: {$location}<br>Tamaño: {$size}")
                                    ->info()
                                    ->duration(5000)
                                    ->send();
                            })
                            ->tooltip('Ver información detallada del archivo'),
                    ])
                    ->columnSpan(1),
                ])->columns(4),
            ])
            ->columns(1)
            ->columnSpanFull()
            ->disabled()
            ->dehydrated(false)
            ->defaultItems(0)
            ->itemLabel(fn (array $state): ?string => $state['document_name'] ?? null)
            ->collapsible()
            ->cloneable(false)
            ->reorderable(false)
            ->addable(false)
            ->deletable(false)
            ->extraAttributes(['style' => 'max-height: 600px; overflow-y: auto;']);
    }
}
