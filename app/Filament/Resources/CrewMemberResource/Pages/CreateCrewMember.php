<?php

namespace App\Filament\Resources\CrewMemberResource\Pages;

use App\Filament\Resources\CrewMemberResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Facades\FilamentView;
use Illuminate\Contracts\View\View;

class CreateCrewMember extends CreateRecord
{
    protected static string $resource = CrewMemberResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Registro de tripulantes creado exitosamente';
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
