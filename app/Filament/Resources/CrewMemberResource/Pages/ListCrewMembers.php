<?php

namespace App\Filament\Resources\CrewMemberResource\Pages;

use App\Filament\Resources\CrewMemberResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Facades\FilamentView;
use Illuminate\Contracts\View\View;

class ListCrewMembers extends ListRecords
{
    protected static string $resource = CrewMemberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Nuevo Registro')
                ->icon('heroicon-o-plus'),
        ];
    }

    public function mount(): void
    {
        parent::mount();
        
        FilamentView::registerRenderHook(
            'panels::body.end',
            fn (): View => view('filament.crew-members.styles')
        );
    }
}
