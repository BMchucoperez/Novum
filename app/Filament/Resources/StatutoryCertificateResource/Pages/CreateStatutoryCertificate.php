<?php

namespace App\Filament\Resources\StatutoryCertificateResource\Pages;

use App\Filament\Resources\StatutoryCertificateResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Facades\FilamentView;
use Illuminate\Contracts\View\View;

class CreateStatutoryCertificate extends CreateRecord
{
    protected static string $resource = StatutoryCertificateResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Certificado estatutario creado exitosamente';
    }

    public function mount(): void
    {
        parent::mount();

        FilamentView::registerRenderHook(
            'panels::body.end',
            fn (): View => view('filament.statutory-certificates.styles')
        );
    }
}
