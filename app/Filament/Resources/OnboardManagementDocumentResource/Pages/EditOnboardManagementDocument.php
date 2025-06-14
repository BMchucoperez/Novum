<?php

namespace App\Filament\Resources\OnboardManagementDocumentResource\Pages;

use App\Filament\Resources\OnboardManagementDocumentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Facades\FilamentView;
use Illuminate\Contracts\View\View;

class EditOnboardManagementDocument extends EditRecord
{
    protected static string $resource = OnboardManagementDocumentResource::class;

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
        return 'Documento de gestión a bordo actualizado exitosamente';
    }

    public function mount(int | string $record): void
    {
        parent::mount($record);
        
        FilamentView::registerRenderHook(
            'panels::body.end',
            fn (): View => view('filament.onboard-management-documents.styles')
        );
    }
}
