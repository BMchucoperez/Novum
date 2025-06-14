<?php

namespace App\Filament\Resources\OnboardManagementDocumentResource\Pages;

use App\Filament\Resources\OnboardManagementDocumentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Facades\FilamentView;
use Illuminate\Contracts\View\View;

class ListOnboardManagementDocuments extends ListRecords
{
    protected static string $resource = OnboardManagementDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Nuevo Documento')
                ->icon('heroicon-o-plus'),
        ];
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
