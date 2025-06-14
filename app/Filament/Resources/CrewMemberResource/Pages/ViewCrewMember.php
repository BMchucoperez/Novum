<?php

namespace App\Filament\Resources\CrewMemberResource\Pages;

use App\Filament\Resources\CrewMemberResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Grid;
use Filament\Support\Facades\FilamentView;
use Illuminate\Contracts\View\View;

class ViewCrewMember extends ViewRecord
{
    protected static string $resource = CrewMemberResource::class;

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
                                    ->label('Fecha')
                                    ->date('d/m/Y')
                                    ->columnSpan([
                                        'default' => 1,
                                        'lg' => 1,
                                    ]),
                                Infolists\Components\TextEntry::make('total_crew')
                                    ->label('Total Tripulantes')
                                    ->getStateUsing(fn ($record): int => $record->total_crew)
                                    ->badge()
                                    ->color('primary')
                                    ->columnSpan([
                                        'default' => 1,
                                        'lg' => 1,
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

                Section::make('Resumen de Tripulación')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make([
                            'default' => 1,
                            'sm' => 2,
                            'md' => 3,
                            'lg' => 3,
                        ])
                            ->schema([
                                Infolists\Components\TextEntry::make('officers_count')
                                    ->label('Oficiales')
                                    ->getStateUsing(fn ($record): int => $record->officers_count)
                                    ->badge()
                                    ->color('success'),
                                Infolists\Components\TextEntry::make('crew_count')
                                    ->label('Tripulación')
                                    ->getStateUsing(fn ($record): int => $record->crew_count)
                                    ->badge()
                                    ->color('warning'),
                                Infolists\Components\TextEntry::make('total_crew')
                                    ->label('Total')
                                    ->getStateUsing(fn ($record): int => $record->total_crew)
                                    ->badge()
                                    ->color('primary'),
                            ]),
                    ]),

                Section::make('Lista de Tripulantes')
                    ->columnSpanFull()
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('tripulantes')
                            ->label('')
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
                                        Infolists\Components\TextEntry::make('cargo')
                                            ->label('Cargo')
                                            ->columnSpan([
                                                'default' => 1,
                                                'md' => 1,
                                                'lg' => 1,
                                            ])
                                            ->weight('semibold')
                                            ->color('primary'),
                                        Infolists\Components\TextEntry::make('nombre')
                                            ->label('Nombre')
                                            ->columnSpan([
                                                'default' => 1,
                                                'md' => 1,
                                                'lg' => 1,
                                            ])
                                            ->weight('medium'),
                                        Infolists\Components\TextEntry::make('matricula')
                                            ->label('N° de Matrícula')
                                            ->placeholder('Sin matrícula')
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
                            ]),
                    ]),

                Section::make('Observaciones Generales')
                    ->columnSpanFull()
                    ->schema([
                        Infolists\Components\TextEntry::make('general_observations')
                            ->label('')
                            ->placeholder('Sin observaciones generales')
                            ->columnSpanFull(),
                    ])
                    ->visible(fn ($record): bool => !empty($record->general_observations)),
            ]);
    }

    public function mount(int | string $record): void
    {
        parent::mount($record);
        
        FilamentView::registerRenderHook(
            'panels::body.end',
            fn (): View => view('filament.crew-members.styles')
        );
    }
}
