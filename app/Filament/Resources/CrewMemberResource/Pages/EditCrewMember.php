<?php

namespace App\Filament\Resources\CrewMemberResource\Pages;

use App\Filament\Resources\CrewMemberResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Facades\FilamentView;
use Illuminate\Contracts\View\View;

class EditCrewMember extends EditRecord
{
    protected static string $resource = CrewMemberResource::class;

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
        return 'Registro de tripulantes actualizado exitosamente';
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
