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
                                    ->description('Tipo de servicio y navegación')
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
                                            ->label('Tipo de Servicio')
                                            ->helperText('Seleccione el tipo de servicio que presta la embarcación'),

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
                                        Forms\Components\TextInput::make('flag_registry')
                                            ->required()
                                            ->maxLength(255)
                                            ->label('Bandera de Registro')
                                            ->placeholder('Ej: Peruana')
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
                    ->label('Tipo de Servicio')
                    ->badge()
                    ->color('success'),

                Tables\Columns\TextColumn::make('navigationType.name')
                    ->searchable()
                    ->sortable()
                    ->label('Tipo de Navegación')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('owner.name')
                    ->searchable()
                    ->sortable()
                    ->label('Propietario')
                    ->limit(30)
                    ->icon('heroicon-o-user'),

                Tables\Columns\TextColumn::make('user.name')
                    ->searchable()
                    ->sortable()
                    ->label('Usuario Asignado')
                    ->limit(30)
                    ->icon('heroicon-o-user-circle'),

                Tables\Columns\TextColumn::make('associated_vessels_count')
                    ->label('Embarcaciones Asociadas')
                    ->state(function (Vessel $record): string {
                        $count = $record->associatedVessels()->count();
                        if ($count === 0) {
                            return 'Ninguna';
                        }
                        return $count . ' asociada' . ($count > 1 ? 's' : '');
                    })
                    ->badge()
                    ->color(fn (string $state): string => $state === 'Ninguna' ? 'gray' : 'success')
                    ->icon('heroicon-o-link'),

                Tables\Columns\TextColumn::make('documents_completeness')
                    ->label('Documentos')
                    ->state(function (Vessel $record): string {
                        $completeness = $record->getDocumentCompleteness();
                        $missing = count($record->getMissingDocuments());
                        return $missing === 0 ? 'Completo' : "{$completeness}% ({$missing} faltantes)";
                    })
                    ->badge()
                    ->color(function (Vessel $record): string {
                        $missing = count($record->getMissingDocuments());
                        if ($missing === 0) return 'success';
                        if ($missing <= 5) return 'warning';
                        return 'danger';
                    })
                    ->icon('heroicon-o-document-text'),

                Tables\Columns\TextColumn::make('construction_year')
                    ->sortable()
                    ->label('Año')
                    ->icon('heroicon-o-calendar'),

                // Columnas adicionales (ocultas por defecto)
                Tables\Columns\TextColumn::make('flag_registry')
                    ->searchable()
                    ->label('Bandera')
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->description(fn (Vessel $record): string => $record->port_registry)
                    ->icon('heroicon-o-flag')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('port_registry')
                    ->searchable()
                    ->label('Puerto')
                    ->icon('heroicon-o-map-pin')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('dimensions')
                    ->label('Dimensiones')
                    ->state(function (Vessel $record): string {
                        return "{$record->length}m × {$record->beam}m × {$record->depth}m";
                    })
                    ->icon('heroicon-o-variable')
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
                    ->label('Tipo de Servicio')
                    ->indicator('Tipo de Servicio')
                    ->multiple()
                    ->preload()
                    ->searchable(),

                Tables\Filters\SelectFilter::make('navigation_type_id')
                    ->relationship('navigationType', 'name')
                    ->label('Tipo de Navegación')
                    ->indicator('Tipo de Navegación')
                    ->multiple()
                    ->preload()
                    ->searchable(),

                Tables\Filters\SelectFilter::make('owner_id')
                    ->relationship('owner', 'name')
                    ->label('Propietario')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('shipyard_id')
                    ->relationship('shipyard', 'name')
                    ->label('Astillero')
                    ->preload()
                    ->searchable(),

                Tables\Filters\SelectFilter::make('user_id')
                    ->relationship('user', 'name', function ($query) {
                        // Solo mostrar usuarios con el rol "Armador"
                        return $query->whereHas('roles', function ($query) {
                            $query->where('name', 'Armador');
                        });
                    })
                    ->label('Usuario Asignado')
                    ->preload()
                    ->searchable(),
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

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

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
                ->disk('local')
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
                ->afterStateUpdated(function ($state, $record, $component) use ($documentType, $category, $documentName) {
                    if ($state && $record) {
                        // El estado puede ser un array o un solo archivo
                        $file = is_array($state) ? (count($state) > 0 ? $state[0] : null) : $state;
                        if ($file) {
                            static::handleDocumentUpload($file, $record, $documentType, $category, $documentName);
                        }
                    }
                })
                ->afterStateHydrated(function ($component, $record) use ($documentType) {
                    if ($record) {
                        $document = $record->getDocumentByType($documentType);
                        if ($document && $document->file_path) {
                            // Para FileUpload, el estado debe ser un array de archivos
                            $component->state([$document->file_path]);
                        } else {
                            // Si no hay documento, estado vacío
                            $component->state([]);
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

            $disk = Storage::disk('local');
            
            // Si es un TemporaryUploadedFile (objeto de Livewire)
            if (is_object($file) && method_exists($file, 'store')) {
                Log::info("Procesando TemporaryUploadedFile: {$file->getClientOriginalName()}");
                
                // Generar un nombre único
                $extension = $file->getClientOriginalExtension();
                $newFileName = $documentType . '_' . $vessel->id . '_' . time() . '.' . $extension;
                $storagePath = "vessel-documents/{$vessel->id}/{$category}/{$newFileName}";
                
                // Usar el método store de Livewire
                $finalPath = $file->storeAs("vessel-documents/{$vessel->id}/{$category}", $newFileName, 'local');
                
                $fileSize = $file->getSize();
                $mimeType = $file->getMimeType();
                $fileName = $newFileName;
                
            } elseif (is_string($file) && file_exists($file)) {
                // Es una ruta de archivo temporal
                Log::info("Procesando archivo temporal: {$file}");
                
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
            VesselDocument::create([
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
            
            // Notificar éxito
            \Filament\Notifications\Notification::make()
                ->title('Documento subido correctamente')
                ->body("Se ha subido: {$documentName}")
                ->success()
                ->send();
            
            Log::info("Documento registrado: {$documentType} para embarcación {$vessel->id}");
            
        } catch (\Exception $e) {
            Log::error("Error procesando documento {$documentType}: " . $e->getMessage());
            
            \Filament\Notifications\Notification::make()
                ->title('Error al subir documento')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}
