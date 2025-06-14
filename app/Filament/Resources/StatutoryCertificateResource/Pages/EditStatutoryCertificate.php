<?php

namespace App\Filament\Resources\StatutoryCertificateResource\Pages;

use App\Filament\Resources\StatutoryCertificateResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Facades\FilamentView;
use Illuminate\Contracts\View\View;

class EditStatutoryCertificate extends EditRecord
{
    protected static string $resource = StatutoryCertificateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Certificado estatutario actualizado exitosamente';
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
