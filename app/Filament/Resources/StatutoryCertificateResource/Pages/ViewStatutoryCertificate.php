<?php

namespace App\Filament\Resources\StatutoryCertificateResource\Pages;

use App\Filament\Resources\StatutoryCertificateResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Tabs;
use Filament\Support\Facades\FilamentView;
use Illuminate\Contracts\View\View;

class ViewStatutoryCertificate extends ViewRecord
{
    protected static string $resource = StatutoryCertificateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Información General')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make([
                            'default' => 1,
                            'sm' => 2,
                            'md' => 2,
                            'lg' => 4,
                        ])
                            ->schema([
                                Infolists\Components\TextEntry::make('owner.name')
                                    ->label('Propietario')
                                    ->columnSpan([
                                        'default' => 1,
                                        'lg' => 2,
                                    ]),
                                Infolists\Components\TextEntry::make('vessel.name')
                                    ->label('Embarcación 1')
                                    ->columnSpan([
                                        'default' => 1,
                                        'lg' => 2,
                                    ]),
                                Infolists\Components\TextEntry::make('inspection_date')
                                    ->label('Fecha de Inspección')
                                    ->date('d/m/Y')
                                    ->columnSpan([
                                        'default' => 1,
                                        'lg' => 1,
                                    ]),
                                Infolists\Components\TextEntry::make('inspector_name')
                                    ->label('Inspector')
                                    ->columnSpan([
                                        'default' => 1,
                                        'lg' => 1,
                                    ]),
                                Infolists\Components\TextEntry::make('inspector_license')
                                    ->label('Licencia del Inspector')
                                    ->columnSpan([
                                        'default' => 1,
                                        'lg' => 2,
                                    ]),
                            ]),

                        // Sección adicional para embarcaciones opcionales
                        Grid::make([
                            'default' => 1,
                            'sm' => 2,
                            'md' => 2,
                            'lg' => 2,
                        ])
                            ->schema([
                                Infolists\Components\TextEntry::make('vessel2.name')
                                    ->label('Embarcación 2')
                                    ->placeholder('No asignada')
                                    ->columnSpan([
                                        'default' => 1,
                                        'lg' => 1,
                                    ]),
                                Infolists\Components\TextEntry::make('vessel3.name')
                                    ->label('Embarcación 3')
                                    ->placeholder('No asignada')
                                    ->columnSpan([
                                        'default' => 1,
                                        'lg' => 1,
                                    ]),
                            ])
                            ->visible(fn ($record) => $record->vessel_2_id || $record->vessel_3_id),
                    ]),

                Tabs::make('Checklist de Certificados')
                    ->columnSpanFull()
                    ->tabs([
                        Tabs\Tab::make('Parte 1 - Certificados de Seguridad')
                            ->icon('heroicon-o-shield-check')
                            ->schema([
                                static::createChecklistInfoSection('parte_1_items', 'Certificados de Seguridad'),
                            ]),
                        Tabs\Tab::make('Parte 2 - Documentación de Matrícula')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                static::createChecklistInfoSection('parte_2_items', 'Documentación de Matrícula'),
                            ]),
                        Tabs\Tab::make('Parte 3 - Documentación de Tripulación')
                            ->icon('heroicon-o-users')
                            ->schema([
                                static::createChecklistInfoSection('parte_3_items', 'Documentación de Tripulación'),
                            ]),
                        Tabs\Tab::make('Parte 4 - Seguros y Despachos')
                            ->icon('heroicon-o-banknotes')
                            ->schema([
                                static::createChecklistInfoSection('parte_4_items', 'Seguros y Despachos'),
                            ]),
                        Tabs\Tab::make('Parte 5 - Registros Ambientales')
                            ->icon('heroicon-o-globe-americas')
                            ->schema([
                                static::createChecklistInfoSection('parte_5_items', 'Registros Ambientales'),
                            ]),
                        Tabs\Tab::make('Parte 6 - Gestión de Seguridad')
                            ->icon('heroicon-o-cog-6-tooth')
                            ->schema([
                                static::createChecklistInfoSection('parte_6_items', 'Gestión de Seguridad'),
                            ]),
                    ]),

                Section::make('Evaluación General')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make([
                            'default' => 1,
                            'sm' => 1,
                            'md' => 2,
                            'lg' => 2,
                        ])
                            ->schema([
                                Infolists\Components\TextEntry::make('overall_status')
                                    ->label('Estado General')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'V' => 'success',
                                        'A' => 'warning',
                                        'N' => 'danger',
                                        'R' => 'danger',
                                        default => 'gray',
                                    })
                                    ->columnSpan([
                                        'default' => 1,
                                        'md' => 1,
                                    ]),
                                Infolists\Components\TextEntry::make('general_observations')
                                    ->label('Observaciones Generales')
                                    ->columnSpan([
                                        'default' => 1,
                                        'md' => 2,
                                    ]),
                            ]),
                    ]),
            ]);
    }

    protected static function createChecklistInfoSection(string $fieldName, string $title): Infolists\Components\RepeatableEntry
    {
        return Infolists\Components\RepeatableEntry::make($fieldName)
            ->label($title)
            ->columnSpanFull()
            ->schema([
                Grid::make([
                    'default' => 1,
                    'sm' => 1,
                    'md' => 3,
                    'lg' => 3,
                    'xl' => 3,
                ])
                    ->schema([
                        Infolists\Components\TextEntry::make('item')
                            ->label('Ítem')
                            ->columnSpan([
                                'default' => 1,
                                'md' => 2,
                                'lg' => 2,
                            ])
                            ->weight('semibold'),
                        Infolists\Components\TextEntry::make('estado')
                            ->label('Estado')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'V' => 'success',
                                'A' => 'warning',
                                'N' => 'danger',
                                'R' => 'danger',
                                default => 'gray',
                            })
                            ->columnSpan([
                                'default' => 1,
                                'md' => 1,
                                'lg' => 1,
                            ]),
                        Infolists\Components\TextEntry::make('comentarios')
                            ->label('Comentarios')
                            ->placeholder('Sin comentarios')
                            ->columnSpan([
                                'default' => 1,
                                'md' => 3,
                                'lg' => 3,
                            ])
                            ->color('gray'),
                    ]),
            ]);
    }

    public function mount(int | string $record): void
    {
        parent::mount($record);

        FilamentView::registerRenderHook(
            'panels::body.end',
            fn (): View => view('filament.statutory-certificates.styles')
        );
    }
}
