<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ChecklistInspectionResource\Pages;
use App\Models\ChecklistInspection;
use App\Models\Vessel;
use App\Models\Owner;
use App\Models\User;
use App\Models\VesselDocument;
use App\Models\VesselDocumentType;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ChecklistInspectionResource extends Resource
{
    protected static ?string $model = ChecklistInspection::class;

    protected static ?string $navigationIcon = 'heroicon-o-check-circle';

    protected static ?string $navigationGroup = 'Inspecciones';

    protected static ?string $navigationLabel = 'Inspecciones Checklist';

    protected static ?string $modelLabel = 'Inspección Checklist';

    protected static ?string $pluralModelLabel = 'Inspecciones Checklist';

    protected static ?int $navigationSort = 5;

    /**
     * Mapeo entre los tipos de documentos de vessel_documents y los ítems del checklist
     */
    protected static function getDocumentItemMapping(): array
    {
        return [
            // PARTE 1 - DOCUMENTOS DE BANDEIRA E APOLICES DE SEGURO
            VesselDocumentType::CERTIFICADO_ARQUEACAO => 'Certificado nacional de arqueação',
            VesselDocumentType::CERTIFICADO_BORDA_LIVRE => 'Certificado nacional de borda livre para a navegação interior',
            VesselDocumentType::PROVISAO_REGISTRO => 'Provisão de registro da propriedade marítima (ou Documento provisório de propiedade)',
            VesselDocumentType::DECLARACAO_CONFORMIDADE => 'Declaração de conformidade para transporte de petróleo',
            VesselDocumentType::CERTIFICADO_SEGURANCA => 'Certificado de segurança de navegação',
            VesselDocumentType::LICENCA_IPAAM => 'Licença de operação - IPAAM',
            VesselDocumentType::AUTORIZACAO_ANP => 'Autorização de ANP',
            VesselDocumentType::AUTORIZACAO_ANTAQ => 'Autorização de ANTAQ',
            VesselDocumentType::AUTORIZACAO_IBAMA => 'Autorização ambiental Para o transporte interestadual de produtos perigosos - IBAMA',
            VesselDocumentType::CERTIFICADO_REGULARIDADE => 'Certificado de regularidade - IBAMA',
            VesselDocumentType::CERTIFICADO_ARMADOR => 'Certificado de registro de armador (CRA)',
            VesselDocumentType::APOLICE_SEGURO => 'Apolice de seguro P&I',
            
            // PARTE 2 - DOCUMENTOS DO SISTEMA DE GESTÃO DE BORDO
            VesselDocumentType::PLANO_SEGURANCA => 'Plano de segurança',
            VesselDocumentType::PLANO_ARRANJO => 'Plano de arranjo geral',
            VesselDocumentType::PLANO_REDE_CARGA => 'Plano de rede de carga e descarga',
            VesselDocumentType::PLANO_CAPACIDADE => 'Plano de caoacidade de tanques',
            VesselDocumentType::CERTIFICADO_PNEUMATICO => 'Certificado de teste pneumático dos tanques de armazenamento de óleo',
            VesselDocumentType::CERTIFICADO_REDE => 'Certificado de Teste da rede de carga / descarga',
            VesselDocumentType::CERTIFICADO_VALVULA => 'Certificado de Teste da válvula de pressão e vácuo ',
            VesselDocumentType::PLANO_SOPEP => 'Plano de Emergência a Bordo para Poluição por Óleo - SOPEP',
            VesselDocumentType::CERTIFICADO_EXTINTORES => 'Certificados de Teste Hidrostático e Manutenção para Extintores de Incêndio',
            
            // DOCUMENTOS EXCLUSIVOS PARA BARCAZAS
            VesselDocumentType::DECLARACAO_CONFORMIDADE => 'Declaração de conformidade para transporte de petróleo',
            
            // DOCUMENTOS EXCLUSIVOS PARA EMPUJADORES
            VesselDocumentType::CARTAO_TRIPULACAO => 'Cartão de tripulação de segurança (CTS)',
            VesselDocumentType::LICENCA_ESTACAO => 'Licença de estação de navio',
            
            // DOCUMENTOS EXCLUSIVOS PARA MOTOCHATAS
            VesselDocumentType::MOTOCHATA_DOCUMENTO_1 => 'Documento especial motochata 1',
            VesselDocumentType::MOTOCHATA_DOCUMENTO_2 => 'Documento especial motochata 2',
        ];
    }

    /**
     * Traducción cosmética de textos en portugués a formato bilingüe para visualización
     * NO modifica los datos almacenados, solo agrega la traducción al español para mostrar
     */
    protected static function translateItemForDisplay(string $item): string
    {
        // Imágenes de banderas desde CDN (flag-icons)
        $flagBR = '<img src="https://flagcdn.com/16x12/br.png" srcset="https://flagcdn.com/32x24/br.png 2x, https://flagcdn.com/48x36/br.png 3x" width="16" height="12" alt="Brasil" style="margin-right:4px;vertical-align:middle;display:inline-block;">';
        $flagPE = '<img src="https://flagcdn.com/16x12/pe.png" srcset="https://flagcdn.com/32x24/pe.png 2x, https://flagcdn.com/48x36/pe.png 3x" width="16" height="12" alt="Perú" style="margin-right:4px;vertical-align:middle;display:inline-block;">';
        
        $translations = [
            // PARTE 1 - DOCUMENTOS DE BANDEIRA E APOLICES DE SEGURO
            'Certificado nacional de arqueação' => $flagBR . 'Certificado nacional de arqueação&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . 'Certificado de Arqueo',
            'Certificado nacional de borda livre para a navegação interior' => $flagBR . 'Certificado nacional de borda livre para a navegação interior&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . 'Certificado de Línea Máxima de Carga',
            'Provisão de registro da propriedade marítima (ou Documento provisório de propiedade)' => $flagBR . 'Provisão de registro da propriedade marítima (ou Documento provisório de propiedade)&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . 'Certificado de Matrícula',
            'Declaração de conformidade para transporte de petróleo' => $flagBR . 'Declaração de conformidade para transporte de petróleo&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . 'Ficha de Registro Medio de Transporte Fluvial (OSINERGMIN)',
            'Certificado de segurança de navegação' => $flagBR . 'Certificado de segurança de navegação&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . 'Certificado Nacional de Seguridad para naves fluviales',
            'Licença de operação - IPAAM' => $flagBR . 'Licença de operação - IPAAM',
            'Autorização de ANP' => $flagBR . 'Autorização de ANP&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . 'Permiso de Operaciones para Prestar Servicio de Transporte Fluvial',
            'Autorização de ANTAQ' => $flagBR . 'Autorização de ANTAQ',
            'Autorização ambiental Para o transporte interestadual de produtos perigosos - IBAMA' => $flagBR . 'Autorização ambiental Para o transporte interestadual de produtos perigosos - IBAMA',
            'Certificado de regularidade - IBAMA' => $flagBR . 'Certificado de regularidade - IBAMA',
            'Certificado de registro de armador (CRA)' => $flagBR . 'Certificado de registro de armador (CRA)',
            'Apolice de seguro P&I' => $flagBR . 'Apólice de seguro P&I&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . 'Póliza de Casco Marítimo P&I',
            
            // PARTE 2 - DOCUMENTOS DO SISTEMA DE GESTÃO DE BORDO
            'Certificado de controle de Praga' => $flagBR . 'Certificado de controle de Praga&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . 'Certificado de Fumigación, Desinfección y Desratización',
            'Plano de segurança' => $flagBR . 'Plano de segurança&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . 'Plano de seguridad',
            'Plano de arranjo geral' => $flagBR . 'Plano de arranjo geral&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . 'Plano de disposición general',
            'Plano de incêndio' => $flagBR . 'Plano de incêndio&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . 'Plano contraincendio',
            'Operador técnico' => $flagBR . 'Operador técnico - DPA&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . 'Operador técnico - DPA',
            'Crew List' => $flagBR . 'Crew List&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . 'Crew List',
            'Plano de rede de carga e descarga' => $flagBR . 'Plano de rede de carga e descarga&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . 'Plano del sistema de carga y descarga',
            'Plano de caoacidade de tanques' => $flagBR . 'Plano de capacidade de tanques&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . 'Plano de disposición de tanques',
            'Certificado de teste pneumático dos tanques de armazenamento de óleo' => $flagBR . 'Certificado de teste pneumático dos tanques de armazenamento de óleo&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . 'Certificado de Prueba de Estanqueidad de los Tanques de Carga',
            'Certificado de Teste da rede de carga / descarga' => $flagBR . 'Certificado de Teste da rede de carga / descarga&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . 'Certificado de Prueba Hidrostática del Sistema de Carga y Descarga',
            'Certificado de Teste da válvula de pressão e vácuo' => $flagBR . 'Certificado de Teste da válvula de pressão e vácuo&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . 'Certificado de Prueba de Válvulas de Presión y Vacío',
            'Certificado de Teste da válvula de pressão e vácuo ' => $flagBR . 'Certificado de Teste da válvula de pressão e vácuo&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . 'Certificado de Prueba de Válvulas de Presión y Vacío',
            'Plano de Emergência a Bordo para Poluição por Óleo - SOPEP' => $flagBR . 'Plano de Emergência a Bordo para Poluição por Óleo - SOPEP&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . 'Plan de emergencia a bordo para casos de derrame de hidrocarburos – Plan SOPEP',
            'Plano de contingência' => $flagBR . 'Plano de contingência&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . 'Plan de contingencia',
            'Certificados de Teste Hidrostático e Manutenção para Extintores de Incêndio' => $flagBR . 'Certificados de Teste Hidrostático e Manutenção para Extintores de Incêndio&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . 'Certificados de Prueba Hidrostática y Mantenimiento de los Extintores',
            
            // DOCUMENTOS EXCLUSIVOS PARA EMPUJADORES
            'Cartão de tripulação de segurança (CTS)' => $flagBR . 'Cartão de tripulação de segurança (CTS)&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . 'Certificado de Dotación Mínima',
            'Licença de estação de navio' => $flagBR . 'Licença de estação de navio&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . 'Permiso para Operar una Estación de Comunicación de Teleservicio Móvil',
        ];

        return $translations[$item] ?? $item;
    }

    /**
     * Obtener documentos existentes para una embarcación específica
     */
    protected static function getVesselDocuments(?int $vesselId): array
    {
        if (!$vesselId) {
            return [];
        }

        return VesselDocument::where('vessel_id', $vesselId)
            ->where('is_valid', true)
            ->get()
            ->keyBy('document_type')
            ->toArray();
    }

    /**
     * Get the Eloquent query for the resource table
     */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        // Check if the current user has the "Armador" role
        if (auth()->user() && auth()->user()->hasRole('Armador')) {
            // For Armador users, only show inspections for barcazas associated with their user account
            $userId = auth()->id();
            
            // Get vessel IDs assigned to this user
            $userVesselIds = Vessel::where('user_id', $userId)->pluck('id')->toArray();
            
            // Filter to only include inspections for these vessels
            $query->whereIn('vessel_id', $userVesselIds);
        }

        return $query;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('📋 Información General de la Inspección')
                    ->description('Complete los datos básicos requeridos para la inspección checklist')
                    ->icon('heroicon-o-information-circle')
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
                                        
                                        // For Armador users, only show vessels assigned to their user account
                                        $query = Vessel::where('owner_id', $ownerId);
                                        
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
                                    ->afterStateUpdated(function (Forms\Set $set, $state) {
                                        if (!$state) {
                                            return;
                                        }
                                        
                                        // Obtener el tipo de embarcación
                                        $vessel = Vessel::find($state);
                                        if (!$vessel || !$vessel->serviceType) {
                                            return;
                                        }
                                        
                                        $vesselType = strtolower($vessel->serviceType->name);
                                        $structure = ChecklistInspection::getDefaultStructure($vesselType);
                                        
                                        // Actualizar cada parte del checklist
                                        for ($i = 1; $i <= 6; $i++) {
                                            $set("parte_{$i}_items", $structure["parte_{$i}"]);
                                        }
                                    })
                                    ->columnSpan([
                                        'default' => 1,
                                        'md' => 1,
                                        'lg' => 1,
                                    ]),

                                Forms\Components\DatePicker::make('inspection_start_date')
                                    ->label('📅 Fecha de Inicio de Inspección')
                                    ->required()
                                    ->prefixIcon('heroicon-o-calendar-days')
                                    ->displayFormat('d/m/Y')
                                    ->columnSpan([
                                        'default' => 1,
                                        'md' => 1,
                                        'lg' => 1,
                                    ]),

                                Forms\Components\DatePicker::make('inspection_end_date')
                                    ->label('📅 Fecha de Fin de Inspección')
                                    ->required()
                                    ->prefixIcon('heroicon-o-calendar-days')
                                    ->displayFormat('d/m/Y')
                                    ->columnSpan([
                                        'default' => 1,
                                        'md' => 1,
                                        'lg' => 1,
                                    ]),

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
                                    ->columnSpan([
                                        'default' => 1,
                                        'md' => 1,
                                        'lg' => 1,
                                    ]),

                                // Forms\Components\TextInput::make('inspector_license')
                                //     ->label('📜 Licencia del Inspector')
                                //     ->required()
                                //     ->maxLength(255)
                                //     ->prefixIcon('heroicon-o-identification')
                                //     ->placeholder('Número de licencia...')
                                //     ->columnSpan([
                                //         'default' => 1,
                                //         'md' => 1,
                                //         'lg' => 1,
                                //     ]),
                            ]),
                    ])
                    ->collapsible()
                    ->persistCollapsed(),

                Tabs::make('Checklist de Inspección')
                    ->columnSpanFull()
                    ->tabs([
                        Tabs\Tab::make('🔍 Parte 1')
                            ->label('DOCUMENTOS DE BANDEIRA E APOLICES DE SEGURO')
                            ->icon('heroicon-o-clipboard-document-check')
                            ->badge(12)
                            ->schema([
                                static::createChecklistSection('parte_1_items', '📋 Items de Evaluación - Parte 1', 1),
                            ]),

                        Tabs\Tab::make('⚙️ Parte 2')
                            ->label('DOCUMENTOS DO SISTEMA DE GESTÃO DE BORDO')
                            ->icon('heroicon-o-clipboard-document-check')
                            ->badge(11)
                            ->schema([
                                static::createChecklistSection('parte_2_items', '🔧 Items de Evaluación - Parte 2', 2),
                            ]),

                        Tabs\Tab::make('🛡️ Parte 3')
                            ->label('CASCO Y ESTRUTURAS')
                            ->icon('heroicon-o-clipboard-document-check')
                            ->badge(25)
                            ->schema([
                                static::createChecklistSection('parte_3_items', '🛡️ Items de Evaluación - Parte 3', 3, true), // true for image-only attachments
                            ]),

                        Tabs\Tab::make('📊 Parte 4')
                            ->label('SISTEMAS DE CARGA E DESCARGA E DE ALARME DE NIVEL')
                            ->icon('heroicon-o-clipboard-document-check')
                            ->badge(22)
                            ->schema([
                                static::createChecklistSection('parte_4_items', '📊 Items de Evaluación - Parte 4', 4, true), // true for image-only attachments
                            ]),

                        Tabs\Tab::make('🔧 Parte 5')
                            ->label('SEGURANÇA, SALVAMENTO, CONTRA INCÊNDIO E LUZES DE NAVEGAÇÃO')
                            ->icon('heroicon-o-clipboard-document-check')
                            ->badge(18)
                            ->schema([
                                static::createChecklistSection('parte_5_items', '🔧 Items de Evaluación - Parte 5', 5, true), // true for image-only attachments
                            ]),

                        Tabs\Tab::make('✅ Parte 6')
                            ->label('SISTEMA DE AMARRAÇÃO')
                            ->icon('heroicon-o-clipboard-document-check')
                            ->badge(8)
                            ->schema([
                                static::createChecklistSection('parte_6_items', '✅ Items de Evaluación - Parte 6', 6, true), // true for image-only attachments
                            ]),
                    ]),

                Section::make('📊 Evaluación General y Conclusiones')
                    ->description('Resumen final de la inspección y observaciones generales')
                    ->icon('heroicon-o-document-text')
                    ->columnSpanFull()
                    ->schema([
                        Forms\Components\Placeholder::make('status_info')
                            ->label('📊 Estado Calculado Automáticamente')
                            ->content('El estado general se calcula automáticamente basado en todos los ítems evaluados')
                            ->extraAttributes([
                                'class' => 'text-sm text-gray-600 bg-blue-50 p-3 rounded-md border border-blue-200'
                            ]),
                            
                        Forms\Components\Textarea::make('general_observations')
                            ->label('📝 Observaciones Generales')
                            ->placeholder('Registre aquí las observaciones generales de la inspección, recomendaciones, puntos importantes a destacar, seguimientos necesarios, etc...')
                            ->rows(6)
                            ->columnSpanFull()
                            ->extraAttributes([
                                'style' => 'resize: vertical; min-height: 120px;'
                            ])
                            ->helperText('ℹ️ Esta sección es para observaciones que aplican a toda la inspección en general'),
                    ])
                    ->collapsible()
                    ->persistCollapsed(),
            ]);
    }

    protected static function createChecklistSection(string $fieldName, string $title, int $parteNumber, bool $imageOnly = false): Repeater
    {
        return Repeater::make($fieldName)
            ->label($title)
            ->schema([
                Section::make()
                    ->schema([
                        Grid::make([
                            'default' => 1,
                            'sm' => 1,
                            'md' => 12,
                            'lg' => 12,
                            'xl' => 12,
                        ])
                            ->schema([
                                // Prioridad (no editable) - Usando Placeholder
                                // Forms\Components\Placeholder::make('prioridad_display')
                                //     ->label('🏅 Prioridad')
                                //     ->content(function (Forms\Get $get) {
                                //         $prioridad = $get('prioridad') ?? 3;
                                //         return match($prioridad) {
                                //             1 => '🔴 Crítica',
                                //             2 => '🟡 Alta',
                                //             3 => '🟢 Media',
                                //             default => 'Sin prioridad'
                                //         };
                                //     })
                                //     ->extraAttributes(function (Forms\Get $get) {
                                //         $prioridad = $get('prioridad') ?? 3;
                                //         $colorClass = match($prioridad) {
                                //             1 => 'text-red-600 bg-red-50 border border-red-200',
                                //             2 => 'text-yellow-600 bg-yellow-50 border border-yellow-200',
                                //             3 => 'text-green-600 bg-green-50 border border-green-200',
                                //             default => 'text-gray-600 bg-gray-50 border border-gray-200'
                                //         };
                                //         return [
                                //             'class' => 'font-semibold px-3 py-2 rounded-md ' . $colorClass
                                //         ];
                                //     })
                                //     ->columnSpan([
                                //         'default' => 1,
                                //         'md' => 2,
                                //         'lg' => 2,
                                //     ]),

                                // Sección de verificación con checkboxes mejorados
                                Section::make()
                                    ->schema([
                                        Grid::make([
                                            'default' => 1,
                                            'md' => 2,
                                        ])
                                            ->schema([
                                                Forms\Components\Checkbox::make('checkbox_1')
                                                    ->label('✅ Cumple')
                                                    ->inline(true)
                                                    ->live()
                                                    ->afterStateUpdated(function (Forms\Set $set, $state, Forms\Get $get) {
                                                        // Si se marca "Cumple", desmarcar "No Cumple" y establecer estado APTO
                                                        if ($state === true) {
                                                            $set('checkbox_2', false);
                                                            $set('estado', 'A'); // A = APTO
                                                        } elseif ($state === false && !$get('checkbox_2')) {
                                                            // Si se desmarca y el otro tampoco está marcado, limpiar estado
                                                            $set('estado', '');
                                                        }
                                                    }),

                                                Forms\Components\Checkbox::make('checkbox_2')
                                                    ->label('❌ No Cumple')
                                                    ->inline(true)
                                                    ->live()
                                                    ->afterStateUpdated(function (Forms\Set $set, $state, Forms\Get $get) {
                                                        // Si se marca "No Cumple", desmarcar "Cumple" y calcular estado según prioridad
                                                        if ($state === true) {
                                                            $set('checkbox_1', false);
                                                            $prioridad = $get('prioridad') ?? 3;
                                                            if ($prioridad === 1) {
                                                                $set('estado', 'N'); // N = NO APTO (Prioridad 1)
                                                            } else {
                                                                $set('estado', 'O'); // O = OBSERVADO (Prioridad 2-3)
                                                            }
                                                        } elseif ($state === false && !$get('checkbox_1')) {
                                                            // Si se desmarca y el otro tampoco está marcado, limpiar estado
                                                            $set('estado', '');
                                                        }
                                                    }),
                                            ]),
                                    ])
                                    ->columnSpan([
                                        'default' => 1,
                                        'md' => 5,
                                        'lg' => 5,
                                    ])
                                    ->compact(),

                                // Estado con colores
                                Forms\Components\Select::make('estado')
                                    ->label('📊 Estado de Evaluación')
                                    ->options([
                                        'A' => 'APTO - Cumple con los requisitos',
                                        'N' => 'NO APTO - No cumple (Prioridad 1)',
                                        'O' => 'OBSERVADO - No cumple (Prioridad 2-3)',
                                    ])
                                    ->prefixIcon('heroicon-o-flag')
                                    ->placeholder('Seleccione el estado...')
                                    ->disabled(function (Forms\Get $get) {
                                        // Deshabilitar si algún checkbox está marcado (estado automático)
                                        return $get('checkbox_1') === true || $get('checkbox_2') === true;
                                    })
                                    ->helperText(function (Forms\Get $get) {
                                        if ($get('checkbox_1') === true || $get('checkbox_2') === true) {
                                            return '✓ Estado establecido automáticamente según evaluación';
                                        }
                                        return 'Seleccione el estado correspondiente o use los checkboxes de evaluación';
                                    })
                                    ->columnSpan([
                                        'default' => 1,
                                        'md' => 4,
                                        'lg' => 4,
                                    ]),

                                // Archivos adjuntos o vista de documento existente
                                Forms\Components\FileUpload::make('archivos_adjuntos')
                                    ->label(function (Forms\Get $get) use ($imageOnly) {
                                        $vesselId = $get('../../vessel_id');
                                        $itemName = $get('item');
                                        
                                        if (!$vesselId || !$itemName) {
                                            return '📁 Archivos Adjuntos';
                                        }
                                        
                                        // Verificar si existe documento
                                        $documentMapping = static::getDocumentItemMapping();
                                        $documentType = array_search($itemName, $documentMapping);
                                        
                                        if ($documentType) {
                                            $document = VesselDocument::where('vessel_id', $vesselId)
                                                ->where('document_type', $documentType)
                                                ->where('is_valid', true)
                                                ->first();
                                            
                                            if ($document) {
                                                return '📄 Documento: ' . $document->getDisplayName();
                                            }
                                        }
                                        
                                        return '📁 Archivos Adjuntos';
                                    })
                                    ->helperText(function (Forms\Get $get) use ($imageOnly) {
                                        $vesselId = $get('../../vessel_id');
                                        $itemName = $get('item');
                                        
                                        if (!$vesselId || !$itemName) {
                                            return 'Suba archivos si es necesario';
                                        }
                                        
                                        // Verificar si existe documento
                                        $documentMapping = static::getDocumentItemMapping();
                                        $documentType = array_search($itemName, $documentMapping);
                                        
                                        if ($documentType) {
                                            $document = VesselDocument::where('vessel_id', $vesselId)
                                                ->where('document_type', $documentType)
                                                ->where('is_valid', true)
                                                ->first();
                                            
                                            if ($document) {
                                                $statusText = $document->getStatusText();
                                                return "✅ Estado: {$statusText}";
                                            }
                                        }
                                        
                                        // Texto con limitaciones técnicas cuando NO hay documento existente
                                        return $imageOnly ? 'archivos permitidos • JPG, PNG | Peso máx: 10MB' : 'archivos permitidos • PDF, JPG, PNG | Peso máx: 10MB';
                                    })
                                    ->multiple()
                                    ->acceptedFileTypes($imageOnly ? ['image/jpeg', 'image/png', 'image/jpg'] : ['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'])
                                    ->maxFiles(5)
                                    ->maxSize(10240) // 10MB
                                    ->directory('checklist-attachments')
                                    ->visibility('private')
                                    ->downloadable()
                                    ->previewable()
                                    ->disabled(function (Forms\Get $get) {
                                        $vesselId = $get('../../vessel_id');
                                        $itemName = $get('item');
                                        
                                        if (!$vesselId || !$itemName) {
                                            return false;
                                        }
                                        
                                        // Deshabilitar si existe documento
                                        $documentMapping = static::getDocumentItemMapping();
                                        $documentType = array_search($itemName, $documentMapping);
                                        
                                        if ($documentType) {
                                            return VesselDocument::where('vessel_id', $vesselId)
                                                ->where('document_type', $documentType)
                                                ->where('is_valid', true)
                                                ->exists();
                                        }
                                        
                                        return false;
                                    })
                                    ->visible(function (Forms\Get $get) {
                                        $vesselId = $get('../../vessel_id');
                                        $itemName = $get('item');
                                        
                                        if (!$vesselId || !$itemName) {
                                            return true;
                                        }
                                        
                                        // Verificar si existe documento
                                        $documentMapping = static::getDocumentItemMapping();
                                        $documentType = array_search($itemName, $documentMapping);
                                        
                                        if ($documentType) {
                                            // Hide if document exists
                                            return !VesselDocument::where('vessel_id', $vesselId)
                                                ->where('document_type', $documentType)
                                                ->where('is_valid', true)
                                                ->exists();
                                        }
                                        
                                        $prioridad = $get('prioridad') ?? 3;
                                        return ChecklistInspection::priorityAllowsAttachments($prioridad);
                                    })
                                    ->columnSpan([
                                        'default' => 1,
                                        'md' => 3,
                                        'lg' => 3,
                                    ]),
                                    
                                // Información sobre documento existente
                                Forms\Components\TextInput::make('document_info')
                                    ->label('Documento Existente')
                                    ->placeholder('Descargar  ->')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->helperText(function (Forms\Get $get) {
                                        $vesselId = $get('../../vessel_id');
                                        $itemName = $get('item');
                                        
                                        if (!$vesselId || !$itemName) {
                                            return '';
                                        }
                                        
                                        $documentMapping = static::getDocumentItemMapping();
                                        $documentType = array_search($itemName, $documentMapping);
                                        
                                        if ($documentType) {
                                            $document = VesselDocument::where('vessel_id', $vesselId)
                                                ->where('document_type', $documentType)
                                                ->where('is_valid', true)
                                                ->first();
                                            
                                            if ($document) {
                                                $statusText = $document->getStatusText();
                                                return "✅ Estado: {$statusText}";
                                            }
                                        }
                                        
                                        return '';
                                    })
                                    ->suffixAction(function (Forms\Get $get) {
                                        $vesselId = $get('../../vessel_id');
                                        $itemName = $get('item');
                                        
                                        if (!$vesselId || !$itemName) {
                                            return null;
                                        }
                                        
                                        $documentMapping = static::getDocumentItemMapping();
                                        $documentType = array_search($itemName, $documentMapping);
                                        
                                        if ($documentType) {
                                            $document = VesselDocument::where('vessel_id', $vesselId)
                                                ->where('document_type', $documentType)
                                                ->where('is_valid', true)
                                                ->first();
                                            
                                            if ($document && $document->file_path) {
                                                $url = route('documents.download', ['id' => $document->id]);
                                                
                                                return Forms\Components\Actions\Action::make('download')
                                                    ->label('Descargar')
                                                    ->icon('heroicon-o-arrow-down-tray')
                                                    ->color('primary')
                                                    ->url($url)
                                                    ->openUrlInNewTab();
                                            }
                                        }
                                        
                                        return null;
                                    })
                                    ->visible(function (Forms\Get $get) {
                                        $vesselId = $get('../../vessel_id');
                                        $itemName = $get('item');
                                        
                                        if (!$vesselId || !$itemName) {
                                            return false;
                                        }
                                        
                                        $documentMapping = static::getDocumentItemMapping();
                                        $documentType = array_search($itemName, $documentMapping);
                                        
                                        if ($documentType) {
                                            return VesselDocument::where('vessel_id', $vesselId)
                                                ->where('document_type', $documentType)
                                                ->where('is_valid', true)
                                                ->exists();
                                        }
                                        
                                        return false;
                                    })
                                    ->columnSpan([
                                        'default' => 1,
                                        'md' => 3,
                                        'lg' => 3,
                                    ]),

                                // Comentarios mejorados
                                Forms\Components\Textarea::make('comentarios')
                                    ->label('💬 Observaciones y Comentarios')
                                    //->placeholder('Registre aquí las observaciones específicas, recomendaciones o detalles importantes sobre este ítem...')
                                    ->rows(3)
                                    ->columnSpan([
                                        'default' => 1,
                                        'md' => 12,
                                        'lg' => 12,
                                    ])
                                    ->extraAttributes([
                                        'style' => 'resize: vertical; min-height: 80px;'
                                    ]),
                            ]),
                    ])
                    ->collapsible()
                    ->persistCollapsed(),
            ])
            ->afterStateHydrated(function (Repeater $component, $state, Forms\Get $get) use ($parteNumber) {
                // Si ya hay estado (editando), no sobrescribir
                if (!empty($state)) {
                    return;
                }
                
                // Para nuevos registros, usar estructura por defecto
                $defaultItems = ChecklistInspection::getDefaultStructure()["parte_{$parteNumber}"];
                $component->state($defaultItems);
            })
            ->addActionLabel("➕ Agregar ítem adicional")
            ->addAction(
                fn (\Filament\Forms\Components\Actions\Action $action) => $action
                    ->icon('heroicon-o-plus-circle')
                    ->color('success')
            )
            ->reorderable(false)
            ->collapsible()
            ->collapsed(true)
            ->itemLabel(function (array $state) {
                $item = $state['item'] ?? 'Nuevo ítem';
                $estado = $state['estado'] ?? '';
                $prioridad = $state['prioridad'] ?? 3;
                
                // Traducir el item para visualización (solo UI, no modifica datos)
                $itemTranslated = static::translateItemForDisplay($item);
                
                // Mostrar prioridad como emoji al lado del nombre del ítem
                $prioridadEmoji = match($prioridad) {
                    1 => '🔴',
                    2 => '🟡',
                    3 => '🟢',
                    default => ''
                };
                
                // Retornar HtmlString con contenedor en una sola línea
                return new HtmlString('<div style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">' . $prioridadEmoji . ' ' . $itemTranslated . '</div>');
            });
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
                    ->label('Embarcación')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('inspection_start_date')
                    ->label('Inicio Inspección')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('inspection_end_date')
                    ->label('Fin Inspección')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('inspector_name')
                    ->label('Inspector')
                    ->searchable(),

                Tables\Columns\BadgeColumn::make('overall_status')
                    ->label('Estado')
                    ->colors([
                        'success' => ['A'],
                        'warning' => ['O'],
                        'danger' => ['N'],
                    ])
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'A' => 'APTO',
                        'N' => 'NO APTO',
                        'O' => 'OBSERVADO',
                        default => $state
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('owner_vessel_filter')
                    ->form([
                        Forms\Components\Grid::make(1)
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

                Tables\Filters\SelectFilter::make('overall_status')
                    ->label('Estado')
                    ->options([
                        'A' => 'APTO - Conforme General',
                        'N' => 'NO APTO - No Conforme Crítico',
                        'O' => 'OBSERVADO - Conforme con Observaciones',
                    ]),
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
            ->emptyStateIcon('heroicon-o-check-circle')
            ->emptyStateHeading('No hay inspecciones checklist registradas')
            ->emptyStateDescription('Crea la primera inspección checklist para comenzar.')
            ->emptyStateActions([
                Tables\Actions\Action::make('create')
                    ->label('Crear inspección')
                    ->url(route('filament.admin.resources.checklist-inspections.create'))
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

    public static function canCreate(): bool
    {
        // Check if the current user has the "Armador" role
        $user = auth()->user();
        
        if ($user && $user->hasRole('Armador')) {
            return false; // Hide create button for Armador role
        }
        
        return true; // Allow create for all other roles
    }

    public static function canDelete($record): bool
    {
        // Check if the current user has the "Armador" role
        $user = auth()->user();
        
        if ($user && $user->hasRole('Armador')) {
            return false; // Hide delete button for Armador role
        }
        
        return true; // Allow delete for all other roles
    }

    // public static function getNavigationBadge(): ?string
    // {
    //     return static::getModel()::count();
    // }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListChecklistInspections::route('/'),
            'create' => Pages\CreateChecklistInspection::route('/create'),
            'view' => Pages\ViewChecklistInspection::route('/{record}'),
            'edit' => Pages\EditChecklistInspection::route('/{record}/edit'),
        ];
    }
}