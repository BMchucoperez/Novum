<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StatutoryCertificateResource\Pages;
use App\Models\StatutoryCertificate;
use App\Models\Vessel;
use App\Models\Owner;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Grid;
use Illuminate\Database\Eloquent\Builder;

class StatutoryCertificateResource extends Resource
{
    protected static ?string $model = StatutoryCertificate::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $navigationGroup = 'Inspecciones';

    protected static ?string $navigationLabel = 'Certificados y Documentos Estatutarios';

    protected static ?string $modelLabel = 'Inspección de Certificados Estatutarios';

    protected static ?string $pluralModelLabel = 'Certificados Estatutarios';

    protected static ?int $navigationSort = 1;



    public static function form(Form $form): Form
    {
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
                                    ->options(Owner::all()->pluck('name', 'id'))
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
                                        return Vessel::where('owner_id', $ownerId)->pluck('name', 'id');
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
                                        return Vessel::pluck('name', 'id')->toArray();
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
                            ]),
                    ]),

                Tabs::make('Checklist de Certificados')
                    ->columnSpanFull()
                    ->tabs([
                        Tabs\Tab::make('Parte 1 - Dirección de Capitanías y Guardacostas - DICAPI')
                            ->icon('heroicon-o-shield-check')
                            ->schema([
                                static::createChecklistSection('parte_1_items', 'Certificados:', 1),
                            ]),

                        Tabs\Tab::make('Parte 2 - Sanidad')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                static::createChecklistSection('parte_2_items', 'Certificado:', 2),
                            ]),

                        Tabs\Tab::make('Parte 3 - Ministerio de Transportes y Comunicaciones - MTC')
                            ->icon('heroicon-o-users')
                            ->schema([
                                static::createChecklistSection('parte_3_items', 'Permisos:', 3),
                            ]),

                        Tabs\Tab::make('Parte 4 - Pólizas de Aseguradoras y Seguros Complementarios')
                            ->icon('heroicon-o-banknotes')
                            ->schema([
                                static::createChecklistSection('parte_4_items', 'Pólizas', 4),
                            ]),

                        Tabs\Tab::make('Parte 5 - Superintendencia Nacional de Aduanas y de Administración Tributaria')
                            ->icon('heroicon-o-globe-americas')
                            ->schema([
                                static::createChecklistSection('parte_5_items', 'Registro:', 5),
                            ]),

                        Tabs\Tab::make('Parte 6 - Agencia Nacional de Transportes Aquavarius (ANTAQ) - Brasil')
                            ->icon('heroicon-o-cog-6-tooth')
                            ->schema([
                                static::createChecklistSection('parte_6_items', 'Registros:', 6),
                            ]),
                    ]),

                Section::make('Evaluación General')
                    ->description('Estado general de la inspección')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make([
                            'default' => 1,
                            'sm' => 1,
                            'md' => 2,
                            'lg' => 2,
                        ])
                            ->schema([
                                // Forms\Components\Select::make('overall_status')
                                //     ->label('Estado General')
                                //     ->options(StatutoryCertificate::getOverallStatusOptions())
                                //     ->required()
                                //     ->default('A')
                                //     ->columnSpan([
                                //         'default' => 1,
                                //         'md' => 1,
                                //     ]),

                                Forms\Components\Textarea::make('general_observations')
                                    ->label('Observaciones Generales')
                                    ->placeholder('Observaciones adicionales sobre la inspección...')
                                    ->rows(4)
                                    ->columnSpan([
                                        'default' => 1,
                                        'md' => 2,
                                    ]),
                            ]),
                    ]),
            ]);
    }

    protected static function createChecklistSection(string $fieldName, string $title, int $parteNumber): Repeater
    {
        $defaultItems = StatutoryCertificate::getDefaultStructure()["parte_{$parteNumber}"];

        return Repeater::make($fieldName)
            ->label($title)
            ->schema([
                Grid::make([
                    'default' => 1,
                    'sm' => 1,
                    'md' => 3,
                    'lg' => 3,
                    'xl' => 3,
                ])
                    ->schema([
                        Forms\Components\TextInput::make('item')
                            ->label('Ítem')
                            ->required()
                            ->columnSpan([
                                'default' => 1,
                                'md' => 2,
                                'lg' => 2,
                            ]),

                        Forms\Components\Select::make('estado')
                            ->label('Estado')
                            ->options(StatutoryCertificate::getStatusOptions())
                            ->required()
                            ->columnSpan([
                                'default' => 1,
                                'md' => 1,
                                'lg' => 1,
                            ]),

                        Forms\Components\DatePicker::make('refrenda')
                            ->label('Refrenda')
                            ->columnSpan([
                                'default' => 1,
                                'md' => 1,
                                'lg' => 1,
                            ]),

                        Forms\Components\Toggle::make('vencimiento_activo')
                            ->label('¿Tiene vencimiento?')
                            ->default(false)
                            ->inline(false)
                            ->reactive()
                            ->columnSpan([
                                'default' => 1,
                                'md' => 1,
                                'lg' => 1,
                            ]),

                        Forms\Components\DatePicker::make('vencimiento')
                            ->label('Vencimiento')
                            ->reactive()
                            ->disabled(fn (Forms\Get $get) => !$get('vencimiento_activo'))
                            ->placeholder('Indeterminado')
                            ->columnSpan([
                                'default' => 1,
                                'md' => 1,
                                'lg' => 1,
                            ]),

                        Forms\Components\Textarea::make('comentarios')
                            ->label('Comentarios')
                            ->placeholder('Observaciones específicas...')
                            ->rows(2)
                            ->columnSpan([
                                'default' => 1,
                                'md' => 3,
                                'lg' => 3,
                            ]),
                    ]),
            ])
            ->defaultItems(count($defaultItems))
            ->default($defaultItems)
            ->addActionLabel("Agregar ítem adicional")
            ->reorderable(false)
            ->collapsible()
            ->itemLabel(fn (array $state): ?string => $state['item'] ?? 'Nuevo ítem')
            ->columnSpanFull();
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
                    ->label('Embarcación Principal')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('associated_vessels')
                    ->label('Embarcaciones Asociadas')
                    ->formatStateUsing(function ($state) {
                        if (empty($state) || !is_array($state)) {
                            return 'Ninguna';
                        }
                        $vessels = \App\Models\Vessel::whereIn('id', $state)->pluck('name')->toArray();
                        return count($vessels) > 0 ? implode(', ', $vessels) : 'Ninguna';
                    })
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('inspection_date')
                    ->label('Fecha de Inspección')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('inspector_name')
                    ->label('Inspector')
                    ->searchable(),

                Tables\Columns\TextColumn::make('inspector_license')
                    ->label('Licencia')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\BadgeColumn::make('overall_status')
                    ->label('Estado')
                    ->colors([
                        'success' => 'V',
                        'warning' => 'A',
                        'danger' => ['N', 'R'],
                    ])
                    ->formatStateUsing(fn (string $state): string => StatutoryCertificate::getOverallStatusOptions()[$state] ?? $state),

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

                Tables\Filters\SelectFilter::make('overall_status')
                    ->label('Estado')
                    ->options(StatutoryCertificate::getOverallStatusOptions()),
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
            ->emptyStateIcon('heroicon-o-clipboard-document-check')
            ->emptyStateHeading('No hay certificados registrados')
            ->emptyStateDescription('Crea el primer certificado estatutario para comenzar.')
            ->emptyStateActions([
                Tables\Actions\Action::make('create')
                    ->label('Crear certificado')
                    ->url(route('filament.admin.resources.statutory-certificates.create'))
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
            'index' => Pages\ListStatutoryCertificates::route('/'),
            'create' => Pages\CreateStatutoryCertificate::route('/create'),
            'view' => Pages\ViewStatutoryCertificate::route('/{record}'),
            'edit' => Pages\EditStatutoryCertificate::route('/{record}/edit'),
        ];
    }
}
