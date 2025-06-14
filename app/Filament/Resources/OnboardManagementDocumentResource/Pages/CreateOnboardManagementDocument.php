<?php

namespace App\Filament\Resources\OnboardManagementDocumentResource\Pages;

use App\Filament\Resources\OnboardManagementDocumentResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Facades\FilamentView;
use Illuminate\Contracts\View\View;

class CreateOnboardManagementDocument extends CreateRecord
{
    protected static string $resource = OnboardManagementDocumentResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Documento de gestión a bordo creado exitosamente';
    }

    public function mount(): void
    {
        parent::mount();
        
        FilamentView::registerRenderHook(
            'panels::body.end',
            fn (): View => view('filament.onboard-management-documents.styles')
        );
    }
}
